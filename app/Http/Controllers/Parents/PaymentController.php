<?php

namespace App\Http\Controllers\Parents;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Payment;
use App\Services\MidtransService;
use App\Services\XenditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    protected MidtransService $midtransService;
    protected XenditService $xenditService;

    public function __construct(MidtransService $midtransService, XenditService $xenditService)
    {
        $this->midtransService = $midtransService;
        $this->xenditService = $xenditService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $parent = $user->parentProfile;

        if (!$parent) {
            return view('parent.payments.index', [
                'bills' => collect(),
                'students' => collect(),
                'total_unpaid' => 0,
                'pending_count' => 0,
                'processing_count' => 0,
            ]);
        }

        $students = $parent->students;
        $studentIds = $students->pluck('id');

        $query = Bill::with(['student', 'billType'])
            ->whereIn('student_id', $studentIds);

        if ($request->filled('status')) {
            if ($request->status === 'unpaid') {
                $query->whereIn('status', ['pending', 'partial', 'overdue']);
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        $bills = $query->orderBy('due_date')->paginate(15)->withQueryString();

        $total_unpaid = Bill::whereIn('student_id', $studentIds)
            ->whereIn('status', ['pending', 'partial', 'overdue'])
            ->sum(DB::raw('total_amount - paid_amount'));
        
        $pending_count = Bill::whereIn('student_id', $studentIds)
            ->whereIn('status', ['pending', 'partial', 'overdue'])
            ->count();
        
        $processing_count = Payment::whereHas('bill', function($q) use ($studentIds) {
            $q->whereIn('student_id', $studentIds);
        })->where('status', 'pending')->count();

        return view('parent.payments.index', compact('bills', 'students', 'total_unpaid', 'pending_count', 'processing_count'));
    }

    public function show(Bill $bill)
    {
        $this->authorizeAccess($bill);

        $bill->load(['student', 'billType', 'payments']);

        return view('parent.payments.show', compact('bill'));
    }

    public function checkout(Bill $bill)
    {
        $this->authorizeAccess($bill);

        if ($bill->status === 'paid') {
            return back()->with('error', 'Tagihan sudah lunas.');
        }

        if ($bill->status === 'cancelled') {
            return back()->with('error', 'Tagihan sudah dibatalkan.');
        }

        $bill->load(['student', 'billType']);
        $amountToPay = $bill->remaining_amount;

        return view('parent.payments.checkout', [
            'bill' => $bill,
            'amount' => $amountToPay,
            'paymentMethods' => $this->xenditService->getAvailablePaymentMethods(),
        ]);
    }

    public function process(Request $request, Bill $bill)
    {
        $this->authorizeAccess($bill);

        if ($bill->status === 'paid') {
            return response()->json(['error' => 'Tagihan sudah lunas'], 400);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1000',
            'payment_gateway' => 'sometimes|in:midtrans,xendit',
            'bank_code' => 'sometimes|string',
        ]);

        $amount = min($validated['amount'], $bill->remaining_amount);
        $gateway = $validated['payment_gateway'] ?? 'xendit'; // Default to Xendit

        DB::beginTransaction();

        try {
            // Create payment record
            $payment = Payment::create([
                'bill_id' => $bill->id,
                'user_id' => Auth::id(),
                'amount' => $amount,
                'status' => 'pending',
                'payment_gateway' => $gateway,
            ]);

            if ($gateway === 'xendit') {
                // Create Xendit Invoice
                $result = $this->xenditService->createInvoice($bill, $payment);

                if (!$result['success']) {
                    throw new \Exception($result['message']);
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'gateway' => 'xendit',
                    'invoice_url' => $result['invoice_url'],
                    'invoice_id' => $result['invoice_id'],
                    'payment_id' => $payment->id,
                ]);
            } else {
                // Create Midtrans Snap Token
                $result = $this->midtransService->createSnapToken($bill, $payment);

                if (!$result['success']) {
                    throw new \Exception($result['message']);
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'gateway' => 'midtrans',
                    'snap_token' => $result['snap_token'],
                    'redirect_url' => $result['redirect_url'],
                    'payment_id' => $payment->id,
                ]);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function finish(Request $request)
    {
        $orderId = $request->get('order_id');
        $transactionStatus = $request->get('transaction_status');

        if ($orderId) {
            $payment = Payment::where('order_id', $orderId)->first();
            
            if ($payment) {
                return redirect()->route('parent.payments.receipt', $payment)
                    ->with('success', 'Pembayaran berhasil diproses.');
            }
        }

        return redirect()->route('parent.payments.index')
            ->with('info', 'Status pembayaran sedang diproses.');
    }

    public function pending(Request $request)
    {
        return redirect()->route('parent.payments.index')
            ->with('warning', 'Pembayaran masih dalam proses. Silakan selesaikan pembayaran Anda.');
    }

    public function error(Request $request)
    {
        return redirect()->route('parent.payments.index')
            ->with('error', 'Pembayaran gagal. Silakan coba lagi.');
    }

    public function receipt(Payment $payment)
    {
        if ($payment->user_id !== Auth::id()) {
            abort(403);
        }

        $payment->load(['bill.student', 'bill.billType']);

        return view('parent.payments.receipt', compact('payment'));
    }

    public function history(Request $request)
    {
        $user = Auth::user();
        $parent = $user->parentProfile;
        $students = $parent ? $parent->students : collect();
        
        $payments = Payment::with(['bill.student', 'bill.billType'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(15);
        
        $total_success = Payment::where('user_id', Auth::id())
            ->where('status', 'success')
            ->sum('amount');
        
        $this_month = Payment::where('user_id', Auth::id())
            ->where('status', 'success')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');

        return view('parent.payments.history', compact('payments', 'students', 'total_success', 'this_month'));
    }

    public function checkStatus(Payment $payment)
    {
        if ($payment->user_id !== Auth::id()) {
            abort(403);
        }

        // Check based on payment gateway
        if ($payment->payment_gateway === 'xendit' && $payment->xendit_invoice_id) {
            $result = $this->xenditService->getInvoiceStatus($payment->xendit_invoice_id);
            
            // Auto-update status if payment is successful
            if ($result['success'] && isset($result['data']['status'])) {
                $xenditStatus = $result['data']['status'];
                
                if (in_array($xenditStatus, ['PAID', 'SETTLED']) && $payment->status !== 'success') {
                    $payment->update([
                        'status' => 'success',
                        'paid_at' => now(),
                        'payment_method' => $result['data']['payment_method'] ?? null,
                        'payment_channel' => $result['data']['payment_channel'] ?? null,
                    ]);

                    // Update bill status
                    $bill = $payment->bill;
                    $totalPaid = $bill->payments()->where('status', 'success')->sum('amount');
                    
                    if ($totalPaid >= $bill->total_amount) {
                        $bill->update(['status' => 'paid', 'paid_amount' => $totalPaid]);
                    } else {
                        $bill->update(['status' => 'partial', 'paid_amount' => $totalPaid]);
                    }

                    $result['payment_updated'] = true;
                    $result['message'] = 'Pembayaran berhasil diverifikasi!';
                } elseif ($xenditStatus === 'EXPIRED' && $payment->status === 'pending') {
                    $payment->update(['status' => 'expired']);
                    $result['payment_updated'] = true;
                }
            }
        } else {
            $result = $this->midtransService->checkStatus($payment->order_id);
        }

        return response()->json($result);
    }

    /**
     * Sync payment status from Xendit (manual trigger)
     */
    public function syncStatus(Payment $payment)
    {
        if ($payment->user_id !== Auth::id()) {
            abort(403);
        }

        if ($payment->payment_gateway !== 'xendit' || !$payment->xendit_invoice_id) {
            return back()->with('error', 'Pembayaran ini tidak menggunakan Xendit.');
        }

        $result = $this->xenditService->getInvoiceStatus($payment->xendit_invoice_id);

        if ($result['success'] && isset($result['data']['status'])) {
            $xenditStatus = $result['data']['status'];
            
            if (in_array($xenditStatus, ['PAID', 'SETTLED'])) {
                $payment->update([
                    'status' => 'success',
                    'paid_at' => now(),
                    'payment_method' => $result['data']['payment_method'] ?? null,
                    'payment_channel' => $result['data']['payment_channel'] ?? null,
                ]);

                // Update bill status
                $bill = $payment->bill;
                $totalPaid = $bill->payments()->where('status', 'success')->sum('amount');
                
                if ($totalPaid >= $bill->total_amount) {
                    $bill->update(['status' => 'paid', 'paid_amount' => $totalPaid]);
                } else {
                    $bill->update(['status' => 'partial', 'paid_amount' => $totalPaid]);
                }

                return back()->with('success', 'Pembayaran berhasil diverifikasi! Status: LUNAS');
            } elseif ($xenditStatus === 'PENDING') {
                return back()->with('info', 'Status pembayaran masih PENDING. Silakan selesaikan pembayaran.');
            } elseif ($xenditStatus === 'EXPIRED') {
                $payment->update(['status' => 'expired']);
                return back()->with('warning', 'Pembayaran sudah EXPIRED. Silakan buat pembayaran baru.');
            }
        }

        return back()->with('error', 'Gagal memeriksa status pembayaran.');
    }

    protected function authorizeAccess(Bill $bill): void
    {
        $user = Auth::user();
        $parent = $user->parentProfile;

        if (!$parent) {
            abort(403, 'Anda tidak memiliki akses ke tagihan ini.');
        }

        $studentIds = $parent->students->pluck('id')->toArray();

        if (!in_array($bill->student_id, $studentIds)) {
            abort(403, 'Anda tidak memiliki akses ke tagihan ini.');
        }
    }
}

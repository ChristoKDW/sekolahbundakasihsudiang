<?php

namespace App\Http\Controllers\Treasurer;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\BillType;
use App\Models\Student;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Mail\PaymentReminderMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class BillController extends Controller
{
    public function index(Request $request)
    {
        $query = Bill::with(['student', 'billType', 'creator']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%")
                            ->orWhere('nis', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('bill_type_id')) {
            $query->where('bill_type_id', $request->bill_type_id);
        }

        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }

        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        $bills = $query->latest()->paginate(15)->withQueryString();
        $billTypes = BillType::where('is_active', true)->get();
        $classes = Student::select('class')->distinct()->orderBy('class')->pluck('class');
        
        $stats = [
            'total' => Bill::count(),
            'paid' => Bill::where('status', 'paid')->count(),
            'pending' => Bill::whereIn('status', ['pending', 'partial'])->count(),
            'overdue' => Bill::where('status', 'overdue')->count(),
        ];

        return view('treasurer.bills.index', compact('bills', 'billTypes', 'stats', 'classes'));
    }

    public function create()
    {
        $students = Student::where('status', 'active')->orderBy('name')->get();
        $billTypes = BillType::where('is_active', true)->get();
        
        return view('treasurer.bills.create', compact('students', 'billTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'bill_type_id' => 'required|exists:bill_types,id',
            'amount' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'fine' => 'nullable|numeric|min:0',
            'due_date' => 'required|date',
            'academic_year' => 'required|string',
            'month' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['discount'] = $validated['discount'] ?? 0;
        $validated['fine'] = $validated['fine'] ?? 0;
        $validated['total_amount'] = $validated['amount'] - $validated['discount'] + $validated['fine'];
        $validated['created_by'] = auth()->id();

        $bill = Bill::create($validated);

        ActivityLog::log('create', 'bills', "Created bill: {$bill->invoice_number}");

        return redirect()->route('treasurer.bills.index')
            ->with('success', 'Tagihan berhasil ditambahkan.');
    }

    public function show(Bill $bill)
    {
        $bill->load(['student', 'billType', 'payments.user', 'creator']);
        
        return view('treasurer.bills.show', compact('bill'));
    }

    public function edit(Bill $bill)
    {
        $students = Student::where('status', 'active')->orderBy('name')->get();
        $billTypes = BillType::where('is_active', true)->get();
        
        return view('treasurer.bills.edit', compact('bill', 'students', 'billTypes'));
    }

    public function update(Request $request, Bill $bill)
    {
        if ($bill->status === 'paid') {
            return back()->with('error', 'Tagihan yang sudah lunas tidak dapat diubah.');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'fine' => 'nullable|numeric|min:0',
            'due_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $oldData = $bill->toArray();

        $validated['discount'] = $validated['discount'] ?? 0;
        $validated['fine'] = $validated['fine'] ?? 0;
        $validated['total_amount'] = $validated['amount'] - $validated['discount'] + $validated['fine'];

        $bill->update($validated);

        ActivityLog::log('update', 'bills', "Updated bill: {$bill->invoice_number}", $oldData, $bill->toArray());

        return redirect()->route('treasurer.bills.index')
            ->with('success', 'Tagihan berhasil diperbarui.');
    }

    public function destroy(Bill $bill)
    {
        if ($bill->paid_amount > 0) {
            return back()->with('error', 'Tagihan yang sudah ada pembayaran tidak dapat dihapus.');
        }

        $invoiceNumber = $bill->invoice_number;
        $bill->delete();

        ActivityLog::log('delete', 'bills', "Deleted bill: {$invoiceNumber}");

        return redirect()->route('treasurer.bills.index')
            ->with('success', 'Tagihan berhasil dihapus.');
    }

    public function generateBulk(Request $request)
    {
        $validated = $request->validate([
            'bill_type_id' => 'required|exists:bill_types,id',
            'class' => 'required|string',
            'due_date' => 'required|date',
            'academic_year' => 'required|string',
            'month' => 'nullable|string',
        ]);

        $billType = BillType::findOrFail($validated['bill_type_id']);
        $students = Student::where('class', $validated['class'])
            ->where('status', 'active')
            ->get();

        $count = 0;
        
        DB::beginTransaction();
        
        try {
            foreach ($students as $student) {
                // Check if bill already exists
                $exists = Bill::where('student_id', $student->id)
                    ->where('bill_type_id', $validated['bill_type_id'])
                    ->where('academic_year', $validated['academic_year'])
                    ->where('month', $validated['month'])
                    ->exists();

                if (!$exists) {
                    Bill::create([
                        'student_id' => $student->id,
                        'bill_type_id' => $validated['bill_type_id'],
                        'amount' => $billType->amount,
                        'total_amount' => $billType->amount,
                        'due_date' => $validated['due_date'],
                        'academic_year' => $validated['academic_year'],
                        'month' => $validated['month'],
                        'created_by' => auth()->id(),
                    ]);
                    $count++;
                }
            }

            DB::commit();

            ActivityLog::log('create', 'bills', "Generated {$count} bulk bills for class {$validated['class']}");

            return back()->with('success', "{$count} tagihan berhasil dibuat.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat tagihan: ' . $e->getMessage());
        }
    }

    public function cancel(Bill $bill)
    {
        if ($bill->paid_amount > 0) {
            return back()->with('error', 'Tagihan yang sudah ada pembayaran tidak dapat dibatalkan.');
        }

        $bill->update(['status' => 'cancelled']);

        ActivityLog::log('update', 'bills', "Cancelled bill: {$bill->invoice_number}");

        return back()->with('success', 'Tagihan berhasil dibatalkan.');
    }

    /**
     * Send payment reminder email to parent
     */
    public function sendReminder(Bill $bill)
    {
        $bill->load(['student.parent', 'billType']);

        if (!$bill->student || !$bill->student->parent) {
            return back()->with('error', 'Data orang tua tidak ditemukan untuk tagihan ini.');
        }

        $parent = $bill->student->parent;

        if (!$parent->email) {
            return back()->with('error', 'Email orang tua belum diisi. Silakan update data orang tua terlebih dahulu.');
        }

        try {
            $daysUntilDue = now()->diffInDays($bill->due_date, false);

            // Send email
            Mail::to($parent->email)->send(new PaymentReminderMail($bill, max(0, $daysUntilDue)));

            // Create in-app notification
            Notification::create([
                'user_id' => $parent->id,
                'type' => 'payment_reminder',
                'title' => 'Pengingat Pembayaran',
                'message' => "Tagihan {$bill->billType->name} untuk {$bill->student->name} " . 
                            ($daysUntilDue > 0 ? "akan jatuh tempo dalam {$daysUntilDue} hari." : "sudah jatuh tempo."),
                'data' => json_encode([
                    'bill_id' => $bill->id,
                    'invoice_number' => $bill->invoice_number,
                ]),
            ]);

            ActivityLog::log('action', 'bills', "Sent payment reminder for bill: {$bill->invoice_number} to {$parent->email}");

            return back()->with('success', "Pengingat pembayaran berhasil dikirim ke {$parent->email}");

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengirim email: ' . $e->getMessage());
        }
    }

    /**
     * Send bulk reminders for selected bills
     */
    public function sendBulkReminders(Request $request)
    {
        $validated = $request->validate([
            'bill_ids' => 'required|array',
            'bill_ids.*' => 'exists:bills,id',
        ]);

        $bills = Bill::with(['student.parent', 'billType'])
            ->whereIn('id', $validated['bill_ids'])
            ->whereIn('status', ['pending', 'partial', 'overdue'])
            ->get();

        $sent = 0;
        $failed = 0;

        foreach ($bills as $bill) {
            if (!$bill->student || !$bill->student->parent || !$bill->student->parent->email) {
                $failed++;
                continue;
            }

            try {
                $parent = $bill->student->parent;
                $daysUntilDue = now()->diffInDays($bill->due_date, false);

                Mail::to($parent->email)->send(new PaymentReminderMail($bill, max(0, $daysUntilDue)));

                Notification::create([
                    'user_id' => $parent->id,
                    'type' => 'payment_reminder',
                    'title' => 'Pengingat Pembayaran',
                    'message' => "Tagihan {$bill->billType->name} untuk {$bill->student->name}.",
                    'data' => json_encode(['bill_id' => $bill->id]),
                ]);

                $sent++;
            } catch (\Exception $e) {
                $failed++;
            }
        }

        ActivityLog::log('action', 'bills', "Sent bulk payment reminders: {$sent} sent, {$failed} failed");

        return back()->with('success', "Pengingat berhasil dikirim ke {$sent} orang tua" . ($failed > 0 ? ", {$failed} gagal." : "."));
    }
}

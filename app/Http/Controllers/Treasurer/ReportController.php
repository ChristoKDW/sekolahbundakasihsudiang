<?php

namespace App\Http\Controllers\Treasurer;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Bill;
use App\Models\BillType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $billTypes = BillType::where('is_active', true)->get();
        
        $query = Payment::with(['bill.student', 'bill.billType'])
            ->where('status', 'success');

        if ($request->filled('start_date')) {
            $query->whereDate('paid_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('paid_at', '<=', $request->end_date);
        }

        if ($request->filled('bill_type')) {
            $query->whereHas('bill', function ($q) use ($request) {
                $q->where('bill_type_id', $request->bill_type);
            });
        }

        $payments = $query->latest('paid_at')->paginate(20)->withQueryString();
        
        $totalIncome = Payment::where('status', 'success')->sum('amount');
        $totalTransactions = Payment::where('status', 'success')->count();
        $billsPaid = Bill::where('status', 'paid')->count();
        
        $summary = [
            'total_income' => $totalIncome,
            'total_transactions' => $totalTransactions,
            'average' => $totalTransactions > 0 ? $totalIncome / $totalTransactions : 0,
            'bills_paid' => $billsPaid,
        ];
        
        // Chart data - monthly income
        $chartData = [
            'labels' => [],
            'data' => [],
        ];
        
        for ($i = 1; $i <= 12; $i++) {
            $chartData['labels'][] = date('M', mktime(0, 0, 0, $i, 1));
            $chartData['data'][] = Payment::where('status', 'success')
                ->whereMonth('paid_at', $i)
                ->whereYear('paid_at', date('Y'))
                ->sum('amount');
        }
        
        // By type data
        $byType = Bill::join('bill_types', 'bills.bill_type_id', '=', 'bill_types.id')
            ->join('payments', 'bills.id', '=', 'payments.bill_id')
            ->where('payments.status', 'success')
            ->select('bill_types.name', DB::raw('sum(payments.amount) as total'))
            ->groupBy('bill_types.id', 'bill_types.name')
            ->get();

        return view('treasurer.reports.index', compact('billTypes', 'payments', 'summary', 'chartData', 'byType'));
    }

    public function payments(Request $request)
    {
        $query = Payment::with(['bill.student', 'bill.billType', 'user'])
            ->where('status', 'success');

        if ($request->filled('start_date')) {
            $query->whereDate('paid_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('paid_at', '<=', $request->end_date);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $payments = $query->latest('paid_at')->paginate(20)->withQueryString();

        $summary = [
            'total_amount' => $query->sum('amount'),
            'total_transactions' => $query->count(),
        ];

        $paymentMethods = Payment::where('status', 'success')
            ->select('payment_method')
            ->distinct()
            ->pluck('payment_method')
            ->filter();

        return view('treasurer.reports.payments', compact('payments', 'summary', 'paymentMethods'));
    }

    public function receivables(Request $request)
    {
        $query = Bill::with(['student', 'billType'])
            ->whereIn('status', ['pending', 'partial', 'overdue']);

        if ($request->filled('class')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('class', $request->class);
            });
        }

        if ($request->filled('bill_type_id')) {
            $query->where('bill_type_id', $request->bill_type_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bills = $query->orderBy('due_date')->paginate(20)->withQueryString();

        $summary = [
            'total_receivables' => $query->sum(DB::raw('total_amount - paid_amount')),
            'total_bills' => $query->count(),
            'overdue_amount' => Bill::where('status', 'overdue')
                ->sum(DB::raw('total_amount - paid_amount')),
        ];

        $classes = \App\Models\Student::select('class')->distinct()->orderBy('class')->pluck('class');
        $billTypes = BillType::where('is_active', true)->get();

        return view('treasurer.reports.receivables', compact('bills', 'summary', 'classes', 'billTypes'));
    }

    public function monthly(Request $request)
    {
        $year = $request->get('year', date('Y'));
        
        $monthlyData = [];
        
        for ($month = 1; $month <= 12; $month++) {
            $payments = Payment::where('status', 'success')
                ->whereMonth('paid_at', $month)
                ->whereYear('paid_at', $year);

            $bills = Bill::whereMonth('created_at', $month)
                ->whereYear('created_at', $year);

            $monthlyData[] = [
                'month' => date('F', mktime(0, 0, 0, $month, 1)),
                'month_number' => $month,
                'billed' => $bills->sum('total_amount'),
                'collected' => $payments->sum('amount'),
                'transactions' => $payments->count(),
            ];
        }

        $summary = [
            'total_billed' => collect($monthlyData)->sum('billed'),
            'total_collected' => collect($monthlyData)->sum('collected'),
            'total_transactions' => collect($monthlyData)->sum('transactions'),
        ];

        return view('treasurer.reports.monthly', compact('monthlyData', 'summary', 'year'));
    }

    public function byClass(Request $request)
    {
        $academicYear = $request->get('academic_year', date('Y') . '/' . (date('Y') + 1));

        $classData = DB::table('bills')
            ->join('students', 'bills.student_id', '=', 'students.id')
            ->where('bills.academic_year', $academicYear)
            ->select(
                'students.class',
                DB::raw('SUM(bills.total_amount) as total_billed'),
                DB::raw('SUM(bills.paid_amount) as total_paid'),
                DB::raw('SUM(bills.total_amount - bills.paid_amount) as outstanding'),
                DB::raw('COUNT(DISTINCT bills.student_id) as student_count')
            )
            ->groupBy('students.class')
            ->orderBy('students.class')
            ->get();

        return view('treasurer.reports.by-class', compact('classData', 'academicYear'));
    }

    public function byType(Request $request)
    {
        $academicYear = $request->get('academic_year', date('Y') . '/' . (date('Y') + 1));

        $typeData = DB::table('bills')
            ->join('bill_types', 'bills.bill_type_id', '=', 'bill_types.id')
            ->where('bills.academic_year', $academicYear)
            ->select(
                'bill_types.name',
                DB::raw('SUM(bills.total_amount) as total_billed'),
                DB::raw('SUM(bills.paid_amount) as total_paid'),
                DB::raw('SUM(bills.total_amount - bills.paid_amount) as outstanding'),
                DB::raw('COUNT(*) as bill_count')
            )
            ->groupBy('bill_types.id', 'bill_types.name')
            ->orderBy('bill_types.name')
            ->get();

        return view('treasurer.reports.by-type', compact('typeData', 'academicYear'));
    }

    public function exportPdf(Request $request)
    {
        // TODO: Install barryvdh/laravel-dompdf package first
        // composer require barryvdh/laravel-dompdf
        
        return back()->with('info', 'Fitur export PDF akan segera tersedia. Silakan install package dompdf terlebih dahulu.');
        
        /* 
        $type = $request->get('type', 'payments');
        
        switch ($type) {
            case 'payments':
                $data = $this->getPaymentsData($request);
                $pdf = Pdf::loadView('treasurer.reports.pdf.payments', $data);
                $filename = 'laporan-pembayaran-' . date('Y-m-d') . '.pdf';
                break;
                
            case 'receivables':
                $data = $this->getReceivablesData($request);
                $pdf = Pdf::loadView('treasurer.reports.pdf.receivables', $data);
                $filename = 'laporan-piutang-' . date('Y-m-d') . '.pdf';
                break;
                
            default:
                return back()->with('error', 'Jenis laporan tidak valid.');
        }

        return $pdf->download($filename);
        */
    }

    protected function getPaymentsData(Request $request): array
    {
        $query = Payment::with(['bill.student', 'bill.billType', 'user'])
            ->where('status', 'success');

        if ($request->filled('start_date')) {
            $query->whereDate('paid_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('paid_at', '<=', $request->end_date);
        }

        return [
            'payments' => $query->latest('paid_at')->get(),
            'summary' => [
                'total_amount' => $query->sum('amount'),
                'total_transactions' => $query->count(),
            ],
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ];
    }

    protected function getReceivablesData(Request $request): array
    {
        $query = Bill::with(['student', 'billType'])
            ->whereIn('status', ['pending', 'partial', 'overdue']);

        return [
            'bills' => $query->orderBy('due_date')->get(),
            'summary' => [
                'total_receivables' => $query->sum(DB::raw('total_amount - paid_amount')),
                'total_bills' => $query->count(),
            ],
        ];
    }

    public function export(Request $request)
    {
        $format = $request->input('format', 'xlsx');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $type = $request->input('type', 'payments');
        
        // For now, just redirect back with info since export packages are not installed
        return back()->with('info', "Fitur export {$format} akan segera tersedia. Silakan install package Laravel Excel atau DomPDF terlebih dahulu.");
    }
}

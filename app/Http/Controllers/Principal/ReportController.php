<?php

namespace App\Http\Controllers\Principal;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\Student;
use App\Models\BillType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $currentYear = date('Y');
        
        $totalStudents = Student::where('status', 'active')->count();
        $totalIncome = Payment::where('status', 'success')
            ->whereYear('paid_at', $currentYear)
            ->sum('amount');
        $totalReceivables = Bill::whereIn('status', ['pending', 'partial', 'overdue'])
            ->sum(DB::raw('total_amount - paid_amount'));
        $collectionRate = $this->calculateCollectionRate();

        return view('principal.reports.index', compact('totalStudents', 'totalIncome', 'totalReceivables', 'collectionRate'));
    }

    public function income(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month');
        $billTypes = BillType::where('is_active', true)->get();

        $query = Payment::with(['bill.student', 'bill.billType'])
            ->where('status', 'success')
            ->whereYear('paid_at', $year);

        if ($month) {
            $query->whereMonth('paid_at', $month);
        }

        $payments = $query->latest('paid_at')->paginate(20)->withQueryString();

        // Calculate yearly income and monthly data
        $yearly_income = Payment::where('status', 'success')
            ->whereYear('paid_at', $year)
            ->sum('amount');
        
        $total_transactions = Payment::where('status', 'success')
            ->whereYear('paid_at', $year)
            ->count();
        
        $monthly_average = $yearly_income / 12;
        
        // Monthly data
        $monthly_data = [];
        $highest_month = ['name' => '-', 'amount' => 0];
        
        for ($m = 1; $m <= 12; $m++) {
            $monthAmount = Payment::where('status', 'success')
                ->whereYear('paid_at', $year)
                ->whereMonth('paid_at', $m)
                ->sum('amount');
            
            $monthTransactions = Payment::where('status', 'success')
                ->whereYear('paid_at', $year)
                ->whereMonth('paid_at', $m)
                ->count();
            
            $monthName = date('F', mktime(0, 0, 0, $m, 1));
            $monthly_data[] = [
                'name' => $monthName,
                'transactions' => $monthTransactions,
                'amount' => $monthAmount,
            ];
            
            if ($monthAmount > $highest_month['amount']) {
                $highest_month = ['name' => $monthName, 'amount' => $monthAmount];
            }
        }

        // Chart data
        $chartData = $this->getMonthlyChartData($year);

        // By payment method
        $byMethod = Payment::where('status', 'success')
            ->whereYear('paid_at', $year)
            ->select('payment_method', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('payment_method')
            ->get();

        // Income by bill type
        $income_by_type = DB::table('payments')
            ->join('bills', 'payments.bill_id', '=', 'bills.id')
            ->join('bill_types', 'bills.bill_type_id', '=', 'bill_types.id')
            ->where('payments.status', 'success')
            ->whereYear('payments.paid_at', $year)
            ->select('bill_types.name', DB::raw('SUM(payments.amount) as total'))
            ->groupBy('bill_types.id', 'bill_types.name')
            ->orderByDesc('total')
            ->get();

        return view('principal.reports.income', compact(
            'payments', 'billTypes', 'chartData', 'byMethod', 'year', 'month',
            'yearly_income', 'monthly_average', 'highest_month', 'total_transactions', 'monthly_data',
            'income_by_type'
        ));
    }

    public function collection(Request $request)
    {
        $academicYear = $request->get('academic_year', date('Y') . '/' . (date('Y') + 1));

        // By class
        $byClass = DB::table('bills')
            ->join('students', 'bills.student_id', '=', 'students.id')
            ->where('bills.academic_year', $academicYear)
            ->select(
                'students.class',
                DB::raw('SUM(bills.total_amount) as total_billed'),
                DB::raw('SUM(bills.paid_amount) as total_paid'),
                DB::raw('ROUND((SUM(bills.paid_amount) / NULLIF(SUM(bills.total_amount), 0)) * 100, 2) as collection_rate')
            )
            ->groupBy('students.class')
            ->orderBy('students.class')
            ->get();

        // By bill type
        $byType = DB::table('bills')
            ->join('bill_types', 'bills.bill_type_id', '=', 'bill_types.id')
            ->where('bills.academic_year', $academicYear)
            ->select(
                'bill_types.name',
                DB::raw('SUM(bills.total_amount) as total_billed'),
                DB::raw('SUM(bills.paid_amount) as total_paid'),
                DB::raw('ROUND((SUM(bills.paid_amount) / NULLIF(SUM(bills.total_amount), 0)) * 100, 2) as collection_rate')
            )
            ->groupBy('bill_types.id', 'bill_types.name')
            ->orderBy('bill_types.name')
            ->get();

        $summary = [
            'total_billed' => $byClass->sum('total_billed'),
            'total_collected' => $byClass->sum('total_paid'),
            'overall_rate' => $byClass->sum('total_billed') > 0 
                ? round(($byClass->sum('total_paid') / $byClass->sum('total_billed')) * 100, 2)
                : 0,
        ];
        
        // Additional variables for view
        $academic_years = Bill::select('academic_year')->distinct()->orderByDesc('academic_year')->pluck('academic_year');
        $current_year = $academicYear;
        $collection_rate = $summary['overall_rate'];
        $total_billed = $summary['total_billed'];
        $total_collected = $summary['total_collected'];
        $total_bills = Bill::where('academic_year', $academicYear)->count();
        $bills_paid = Bill::where('academic_year', $academicYear)->where('status', 'paid')->count();
        $bills_pending = Bill::where('academic_year', $academicYear)->whereIn('status', ['pending', 'partial'])->count();
        $bills_overdue = Bill::where('academic_year', $academicYear)->where('status', 'overdue')->count();
        $total_outstanding = Bill::where('academic_year', $academicYear)->whereIn('status', ['pending', 'partial', 'overdue'])->sum(DB::raw('total_amount - paid_amount'));
        $total_overdue = Bill::where('academic_year', $academicYear)->where('status', 'overdue')->sum(DB::raw('total_amount - paid_amount'));
        $collection_by_class = $byClass;
        $collection_by_type = $byType;

        return view('principal.reports.collection', compact(
            'byClass', 'byType', 'summary', 'academicYear',
            'academic_years', 'current_year', 'collection_rate',
            'total_billed', 'total_bills', 'total_collected', 'bills_paid',
            'total_outstanding', 'bills_pending', 'total_overdue', 'bills_overdue', 'collection_by_class',
            'collection_by_type'
        ));
    }

    public function outstanding(Request $request)
    {
        $query = Bill::with(['student', 'billType'])
            ->whereIn('status', ['pending', 'partial', 'overdue']);

        if ($request->filled('class')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('class', $request->class);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bills = $query->orderBy('due_date')->paginate(20)->withQueryString();

        $total_outstanding = Bill::whereIn('status', ['pending', 'partial', 'overdue'])
            ->sum(DB::raw('total_amount - paid_amount'));
        
        // Not due yet (due_date >= today)
        $not_due_amount = Bill::whereIn('status', ['pending', 'partial'])
            ->where('due_date', '>=', now())
            ->sum(DB::raw('total_amount - paid_amount'));
        
        // Overdue 1-30 days
        $overdue_1_30 = Bill::where('status', 'overdue')
            ->where('due_date', '>=', now()->subDays(30))
            ->where('due_date', '<', now())
            ->sum(DB::raw('total_amount - paid_amount'));
        
        // Overdue 31-60 days
        $overdue_31_60 = Bill::where('status', 'overdue')
            ->where('due_date', '>=', now()->subDays(60))
            ->where('due_date', '<', now()->subDays(30))
            ->sum(DB::raw('total_amount - paid_amount'));
        
        // Overdue 60+ days
        $overdue_60_plus = Bill::where('status', 'overdue')
            ->where('due_date', '<', now()->subDays(60))
            ->sum(DB::raw('total_amount - paid_amount'));
        
        $overdue_30_plus = $overdue_31_60 + $overdue_60_plus;

        $classes = Student::select('class')->distinct()->orderBy('class')->pluck('class');
        $billTypes = BillType::where('is_active', true)->get();

        // Outstanding by class
        $by_class = DB::table('bills')
            ->join('students', 'bills.student_id', '=', 'students.id')
            ->whereIn('bills.status', ['pending', 'partial', 'overdue'])
            ->select(
                'students.class',
                DB::raw('COUNT(DISTINCT students.id) as student_count'),
                DB::raw('COUNT(bills.id) as bill_count'),
                DB::raw('SUM(bills.total_amount - bills.paid_amount) as outstanding')
            )
            ->groupBy('students.class')
            ->orderBy('students.class')
            ->get();

        return view('principal.reports.outstanding', compact(
            'bills', 'classes', 'billTypes',
            'total_outstanding', 'not_due_amount', 'overdue_1_30', 'overdue_30_plus',
            'overdue_31_60', 'overdue_60_plus', 'by_class'
        ));
    }

    public function trends(Request $request)
    {
        $years = Payment::where('status', 'success')
            ->selectRaw('YEAR(paid_at) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        $trendsData = [];
        
        foreach ($years as $year) {
            $monthly = [];
            for ($m = 1; $m <= 12; $m++) {
                $monthly[] = Payment::where('status', 'success')
                    ->whereYear('paid_at', $year)
                    ->whereMonth('paid_at', $m)
                    ->sum('amount');
            }
            $trendsData[$year] = $monthly;
        }

        return view('principal.reports.trends', compact('trendsData', 'years'));
    }

    protected function calculateCollectionRate(): float
    {
        $totalBilled = Bill::sum('total_amount');
        $totalPaid = Bill::sum('paid_amount');

        if ($totalBilled == 0) {
            return 0;
        }

        return round(($totalPaid / $totalBilled) * 100, 2);
    }

    protected function getMonthlyChartData(int $year): array
    {
        $labels = [];
        $data = [];

        for ($i = 1; $i <= 12; $i++) {
            $labels[] = date('M', mktime(0, 0, 0, $i, 1));
            $data[] = Payment::where('status', 'success')
                ->whereMonth('paid_at', $i)
                ->whereYear('paid_at', $year)
                ->sum('amount');
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }
}

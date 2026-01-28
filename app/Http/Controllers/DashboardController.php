<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return $this->adminDashboard();
        } elseif ($user->isParent()) {
            return $this->parentDashboard();
        } elseif ($user->isPrincipal()) {
            return $this->principalDashboard();
        } elseif ($user->isTreasurer()) {
            return $this->treasurerDashboard();
        }

        return view('dashboard.index');
    }

    protected function adminDashboard()
    {
        $data = [
            'total_students' => Student::where('status', 'active')->count(),
            'total_users' => User::where('is_active', true)->count(),
            'total_bills' => Bill::whereIn('status', ['pending', 'partial', 'overdue'])->count(),
            'total_payments_today' => Payment::where('status', 'success')
                ->whereDate('paid_at', today())
                ->sum('amount'),
            'recent_activities' => \App\Models\ActivityLog::with('user')
                ->latest()
                ->take(10)
                ->get(),
            'users_by_role' => DB::table('user_role')
                ->join('roles', 'roles.id', '=', 'user_role.role_id')
                ->select('roles.display_name', DB::raw('count(*) as total'))
                ->groupBy('roles.id', 'roles.display_name')
                ->get(),
        ];

        return view('dashboard.admin', $data);
    }

    protected function parentDashboard()
    {
        $user = Auth::user();
        $parent = $user->parentProfile;

        $students = $parent ? $parent->students : collect();
        $studentIds = $students->pluck('id');

        $data = [
            'students' => $students,
            'unpaid_bills' => Bill::whereIn('student_id', $studentIds)
                ->whereIn('status', ['pending', 'partial', 'overdue'])
                ->with(['student', 'billType'])
                ->orderBy('due_date')
                ->get(),
            'recent_payments' => Payment::where('user_id', $user->id)
                ->with('bill.student', 'bill.billType')
                ->latest()
                ->take(5)
                ->get(),
            'total_unpaid' => Bill::whereIn('student_id', $studentIds)
                ->whereIn('status', ['pending', 'partial', 'overdue'])
                ->sum(DB::raw('total_amount - paid_amount')),
            'total_paid_this_month' => Payment::where('user_id', $user->id)
                ->where('status', 'success')
                ->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->sum('amount'),
        ];

        return view('dashboard.parent', $data);
    }

    protected function principalDashboard()
    {
        $currentYear = date('Y');
        $currentMonth = date('m');

        $data = [
            'total_students' => Student::where('status', 'active')->count(),
            'monthly_income' => Payment::where('status', 'success')
                ->whereMonth('paid_at', $currentMonth)
                ->whereYear('paid_at', $currentYear)
                ->sum('amount'),
            'yearly_income' => Payment::where('status', 'success')
                ->whereYear('paid_at', $currentYear)
                ->sum('amount'),
            'pending_bills' => Bill::whereIn('status', ['pending', 'overdue'])->count(),
            'collection_rate' => $this->calculateCollectionRate(),
            'monthly_chart_data' => $this->getMonthlyChartData($currentYear),
            'payment_by_type' => Bill::join('bill_types', 'bills.bill_type_id', '=', 'bill_types.id')
                ->join('payments', 'bills.id', '=', 'payments.bill_id')
                ->where('payments.status', 'success')
                ->whereYear('payments.paid_at', $currentYear)
                ->select('bill_types.name', DB::raw('sum(payments.amount) as total'))
                ->groupBy('bill_types.id', 'bill_types.name')
                ->get(),
        ];

        return view('dashboard.principal', $data);
    }

    protected function treasurerDashboard()
    {
        $currentMonth = date('m');
        $currentYear = date('Y');

        $data = [
            'total_bills' => Bill::count(),
            'pending_bills' => Bill::whereIn('status', ['pending', 'partial'])->count(),
            'overdue_bills' => Bill::where('status', 'overdue')->count(),
            'paid_bills' => Bill::where('status', 'paid')->count(),
            'total_receivables' => Bill::whereIn('status', ['pending', 'partial', 'overdue'])
                ->sum(DB::raw('total_amount - paid_amount')),
            'today_income' => Payment::where('status', 'success')
                ->whereDate('paid_at', today())
                ->sum('amount'),
            'monthly_income' => Payment::where('status', 'success')
                ->whereMonth('paid_at', $currentMonth)
                ->whereYear('paid_at', $currentYear)
                ->sum('amount'),
            'recent_payments' => Payment::with('bill.student', 'bill.billType', 'user')
                ->where('status', 'success')
                ->latest('paid_at')
                ->take(10)
                ->get(),
            'overdue_list' => Bill::with('student', 'billType')
                ->where('status', 'overdue')
                ->orderBy('due_date')
                ->take(10)
                ->get(),
        ];

        return view('dashboard.treasurer', $data);
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
        $months = [];
        $incomes = [];

        for ($i = 1; $i <= 12; $i++) {
            $months[] = date('M', mktime(0, 0, 0, $i, 1));
            $incomes[] = Payment::where('status', 'success')
                ->whereMonth('paid_at', $i)
                ->whereYear('paid_at', $year)
                ->sum('amount');
        }

        return [
            'labels' => $months,
            'data' => $incomes,
        ];
    }
}

<?php

namespace App\Http\Controllers\Treasurer;

use App\Http\Controllers\Controller;
use App\Models\PaymentReconciliation;
use App\Models\ReconciliationItem;
use App\Services\ReconciliationService;
use Illuminate\Http\Request;

class ReconciliationController extends Controller
{
    protected ReconciliationService $reconciliationService;

    public function __construct(ReconciliationService $reconciliationService)
    {
        $this->reconciliationService = $reconciliationService;
    }

    public function index(Request $request)
    {
        $reconciliations = PaymentReconciliation::with('processedBy')
            ->latest()
            ->paginate(15);
        
        $totalReconciliations = PaymentReconciliation::count();
        $completedReconciliations = PaymentReconciliation::where('status', 'completed')->count();
        $withIssues = PaymentReconciliation::where('unmatched_count', '>', 0)->count();
        
        $stats = [
            'total' => $totalReconciliations,
            'completed' => $completedReconciliations,
            'with_issues' => $withIssues,
            'match_rate' => $totalReconciliations > 0 
                ? round(($completedReconciliations / $totalReconciliations) * 100, 1) 
                : 0,
        ];

        return view('treasurer.reconciliation.index', compact('reconciliations', 'stats'));
    }

    public function create()
    {
        return view('treasurer.reconciliation.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'source' => 'required|in:api,file',
            'report_file' => 'nullable|file|mimes:csv,xlsx,xls|max:5120',
        ]);

        try {
            // If file uploaded, process file-based reconciliation
            if ($request->hasFile('report_file') && $validated['source'] === 'file') {
                $reconciliation = $this->reconciliationService->runReconciliationFromFile(
                    $request->file('report_file'),
                    $validated['start_date'],
                    $validated['end_date']
                );
            } else {
                // API-based reconciliation
                $reconciliation = $this->reconciliationService->runReconciliation(
                    $validated['start_date'],
                    $validated['end_date']
                );
            }

            return redirect()->route('treasurer.reconciliation.show', $reconciliation)
                ->with('success', 'Rekonsiliasi berhasil dijalankan.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menjalankan rekonsiliasi: ' . $e->getMessage());
        }
    }

    public function show(PaymentReconciliation $reconciliation)
    {
        $reconciliation->load(['items.payment', 'processedBy']);

        $summary = [
            'matched' => $reconciliation->items()->where('match_status', 'matched')->count(),
            'amount_mismatch' => $reconciliation->items()->where('match_status', 'amount_mismatch')->count(),
            'not_found' => $reconciliation->items()->where('match_status', 'not_found')->count(),
            'duplicate' => $reconciliation->items()->where('match_status', 'duplicate')->count(),
        ];
        
        $matchedResults = $reconciliation->items()->where('match_status', 'matched')->with('payment')->get();
        $unmatchedResults = $reconciliation->items()->where('match_status', '!=', 'matched')->with('payment')->get();

        return view('treasurer.reconciliation.show', compact('reconciliation', 'summary', 'matchedResults', 'unmatchedResults'));
    }

    public function resolveItem(Request $request, ReconciliationItem $item)
    {
        $validated = $request->validate([
            'payment_id' => 'nullable|exists:payments,id',
            'resolution' => 'required|string',
        ]);

        try {
            $this->reconciliationService->resolveUnmatchedItem(
                $item->id,
                $validated['payment_id'] ?? null,
                $validated['resolution']
            );

            return back()->with('success', 'Item berhasil di-resolve.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal resolve item: ' . $e->getMessage());
        }
    }

    public function report(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $report = $this->reconciliationService->getSummaryReport($startDate, $endDate);

        return view('treasurer.reconciliation.report', compact('report', 'startDate', 'endDate'));
    }
}

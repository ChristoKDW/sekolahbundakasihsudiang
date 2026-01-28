<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\PaymentReconciliation;
use App\Models\ReconciliationItem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class ReconciliationService
{
    protected MidtransService $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /**
     * Run reconciliation matching algorithm
     * 
     * Algorithm: Two-way matching with exact, fuzzy, and partial matching
     * 1. Exact Match: Match by order_id and amount
     * 2. ID Match: Match by order_id only (check for amount discrepancy)
     * 3. Amount Match: Match by amount and date range
     * 4. Unmatched: Flag for manual review
     */
    public function runReconciliation(string $startDate, string $endDate = null): PaymentReconciliation
    {
        // If endDate is not provided, use startDate as both
        $endDate = $endDate ?? $startDate;
        
        DB::beginTransaction();

        try {
            // Create reconciliation record
            $reconciliation = PaymentReconciliation::create([
                'reconciliation_date' => now(),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => 'processing',
                'total_transactions' => 0,
                'matched_transactions' => 0,
                'unmatched_transactions' => 0,
                'total_amount' => 0,
                'matched_amount' => 0,
                'unmatched_amount' => 0,
            ]);

            // Get Midtrans transactions for the date range
            $midtransData = $this->getMidtransTransactions($startDate, $endDate);
            
            // Get system payments for the date range
            $systemPayments = $this->getSystemPayments($startDate, $endDate);

            $result = $this->processMatching($reconciliation, $midtransData, $systemPayments);

            $reconciliation->update([
                'total_transactions' => $result['total'],
                'matched_transactions' => $result['matched'],
                'unmatched_transactions' => $result['unmatched'],
                'total_amount' => $result['total_amount'],
                'matched_amount' => $result['matched_amount'],
                'unmatched_amount' => $result['unmatched_amount'],
                'status' => 'completed',
                'processed_by' => auth()->id(),
                'processed_at' => now(),
                'details' => [
                    'match_rate' => $result['total'] > 0 
                        ? round(($result['matched'] / $result['total']) * 100, 2) 
                        : 0,
                    'processing_time' => now()->toISOString(),
                    'source' => 'api',
                ],
            ]);

            DB::commit();

            return $reconciliation;

        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Reconciliation Error', [
                'message' => $e->getMessage(),
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

            throw $e;
        }
    }

    /**
     * Run reconciliation from uploaded file
     */
    public function runReconciliationFromFile(UploadedFile $file, string $startDate, string $endDate): PaymentReconciliation
    {
        DB::beginTransaction();

        try {
            // Store the uploaded file
            $filename = 'reconciliation_' . now()->format('Y-m-d_His') . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('reconciliation-reports', $filename, 'public');

            // Create reconciliation record
            $reconciliation = PaymentReconciliation::create([
                'reconciliation_date' => now(),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => 'processing',
                'report_file' => $path,
                'total_transactions' => 0,
                'matched_transactions' => 0,
                'unmatched_transactions' => 0,
                'total_amount' => 0,
                'matched_amount' => 0,
                'unmatched_amount' => 0,
            ]);

            // Parse the uploaded file
            $midtransData = $this->parseReportFile($file);
            
            // Get system payments for the date range
            $systemPayments = $this->getSystemPayments($startDate, $endDate);

            $result = $this->processMatching($reconciliation, $midtransData, $systemPayments);

            $reconciliation->update([
                'total_transactions' => $result['total'],
                'matched_transactions' => $result['matched'],
                'unmatched_transactions' => $result['unmatched'],
                'total_amount' => $result['total_amount'],
                'matched_amount' => $result['matched_amount'],
                'unmatched_amount' => $result['unmatched_amount'],
                'status' => 'completed',
                'processed_by' => auth()->id(),
                'processed_at' => now(),
                'details' => [
                    'match_rate' => $result['total'] > 0 
                        ? round(($result['matched'] / $result['total']) * 100, 2) 
                        : 0,
                    'processing_time' => now()->toISOString(),
                    'source' => 'file',
                    'filename' => $filename,
                ],
            ]);

            DB::commit();

            return $reconciliation;

        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Reconciliation From File Error', [
                'message' => $e->getMessage(),
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

            throw $e;
        }
    }

    /**
     * Parse uploaded report file (CSV/Excel)
     */
    protected function parseReportFile(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $transactions = [];

        if ($extension === 'csv') {
            $transactions = $this->parseCsvFile($file);
        } elseif (in_array($extension, ['xlsx', 'xls'])) {
            $transactions = $this->parseExcelFile($file);
        }

        if (empty($transactions)) {
            throw new Exception('Tidak ada transaksi valid ditemukan dalam file. Pastikan format: transaction_id, order_id, amount, status');
        }

        return $transactions;
    }

    /**
     * Parse CSV file
     */
    protected function parseCsvFile(UploadedFile $file): array
    {
        $transactions = [];
        $handle = fopen($file->getPathname(), 'r');
        $header = fgetcsv($handle); // Skip header row
        
        while (($row = fgetcsv($handle)) !== false) {
            // Expected CSV format: transaction_id, order_id, amount, status, transaction_time
            if (count($row) >= 3) {
                // Check if status indicates a successful transaction
                $status = $row[3] ?? 'settlement';
                if (in_array(strtolower($status), ['settlement', 'capture', 'success'])) {
                    $transactions[] = [
                        'transaction_id' => $row[0] ?? '',
                        'order_id' => $row[1] ?? '',
                        'amount' => (float) preg_replace('/[^0-9.]/', '', $row[2] ?? 0),
                    ];
                }
            }
        }
        fclose($handle);

        return $transactions;
    }

    /**
     * Parse Excel file using PhpSpreadsheet
     */
    protected function parseExcelFile(UploadedFile $file): array
    {
        $transactions = [];
        
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        // Skip header row
        $isFirstRow = true;
        foreach ($rows as $row) {
            if ($isFirstRow) {
                $isFirstRow = false;
                continue;
            }

            // Expected format: transaction_id, order_id, amount, status
            if (count($row) >= 3 && !empty($row[0])) {
                $status = $row[3] ?? 'settlement';
                if (in_array(strtolower((string)$status), ['settlement', 'capture', 'success'])) {
                    $transactions[] = [
                        'transaction_id' => (string)($row[0] ?? ''),
                        'order_id' => (string)($row[1] ?? ''),
                        'amount' => (float) preg_replace('/[^0-9.]/', '', (string)($row[2] ?? 0)),
                    ];
                }
            }
        }

        return $transactions;
    }

    /**
     * Process matching for transactions
     */
    protected function processMatching(PaymentReconciliation $reconciliation, array $midtransData, Collection $systemPayments): array
    {
        $totalTransactions = count($midtransData);
        $matchedCount = 0;
        $unmatchedCount = 0;
        $totalAmount = 0;
        $matchedAmount = 0;
        $unmatchedAmount = 0;

        foreach ($midtransData as $midtransTransaction) {
            $totalAmount += $midtransTransaction['amount'];
            
            $matchResult = $this->matchTransaction($midtransTransaction, $systemPayments);

            ReconciliationItem::create([
                'reconciliation_id' => $reconciliation->id,
                'payment_id' => $matchResult['payment_id'],
                'midtrans_transaction_id' => $midtransTransaction['transaction_id'],
                'midtrans_order_id' => $midtransTransaction['order_id'],
                'midtrans_amount' => $midtransTransaction['amount'],
                'system_amount' => $matchResult['system_amount'],
                'match_status' => $matchResult['status'],
                'notes' => $matchResult['notes'],
            ]);

            if ($matchResult['status'] === 'matched') {
                $matchedCount++;
                $matchedAmount += $midtransTransaction['amount'];
            } else {
                $unmatchedCount++;
                $unmatchedAmount += $midtransTransaction['amount'];
            }
        }

        return [
            'total' => $totalTransactions,
            'matched' => $matchedCount,
            'unmatched' => $unmatchedCount,
            'total_amount' => $totalAmount,
            'matched_amount' => $matchedAmount,
            'unmatched_amount' => $unmatchedAmount,
        ];
    }

    /**
     * Match single transaction using multi-level matching algorithm
     */
    protected function matchTransaction(array $midtransTransaction, Collection $systemPayments): array
    {
        $orderId = $midtransTransaction['order_id'];
        $amount = $midtransTransaction['amount'];
        $transactionId = $midtransTransaction['transaction_id'];

        // Level 1: Exact Match (order_id + amount)
        $exactMatch = $systemPayments->first(function ($payment) use ($orderId, $amount) {
            return $payment->order_id === $orderId && 
                   (float) $payment->amount === (float) $amount;
        });

        if ($exactMatch) {
            return [
                'payment_id' => $exactMatch->id,
                'system_amount' => $exactMatch->amount,
                'status' => 'matched',
                'notes' => 'Exact match by order_id and amount',
            ];
        }

        // Level 2: ID Match (order_id only - check for amount mismatch)
        $idMatch = $systemPayments->first(function ($payment) use ($orderId) {
            return $payment->order_id === $orderId;
        });

        if ($idMatch) {
            $difference = abs((float) $amount - (float) $idMatch->amount);
            return [
                'payment_id' => $idMatch->id,
                'system_amount' => $idMatch->amount,
                'status' => 'amount_mismatch',
                'notes' => "Order ID matched but amount differs by Rp " . number_format($difference, 0, ',', '.'),
            ];
        }

        // Level 3: Transaction ID Match
        $trxMatch = $systemPayments->first(function ($payment) use ($transactionId) {
            return $payment->midtrans_transaction_id === $transactionId;
        });

        if ($trxMatch) {
            if ((float) $trxMatch->amount === (float) $amount) {
                return [
                    'payment_id' => $trxMatch->id,
                    'system_amount' => $trxMatch->amount,
                    'status' => 'matched',
                    'notes' => 'Matched by transaction_id',
                ];
            } else {
                return [
                    'payment_id' => $trxMatch->id,
                    'system_amount' => $trxMatch->amount,
                    'status' => 'amount_mismatch',
                    'notes' => 'Transaction ID matched but amount differs',
                ];
            }
        }

        // Level 4: Check for potential duplicates
        $duplicateCheck = $systemPayments->filter(function ($payment) use ($amount) {
            return (float) $payment->amount === (float) $amount;
        });

        if ($duplicateCheck->count() > 1) {
            return [
                'payment_id' => null,
                'system_amount' => null,
                'status' => 'duplicate',
                'notes' => "Multiple payments with same amount found. Manual review required.",
            ];
        }

        // No match found
        return [
            'payment_id' => null,
            'system_amount' => null,
            'status' => 'not_found',
            'notes' => 'No matching payment found in system',
        ];
    }

    /**
     * Get Midtrans transactions for reconciliation
     */
    protected function getMidtransTransactions(string $startDate, string $endDate): array
    {
        $result = $this->midtransService->getTransactions($startDate, $endDate);
        
        if (!$result['success']) {
            // Fallback: get from payments table with midtrans data
            return Payment::whereDate('paid_at', '>=', $startDate)
                ->whereDate('paid_at', '<=', $endDate)
                ->where('status', 'success')
                ->whereNotNull('midtrans_transaction_id')
                ->get()
                ->map(function ($payment) {
                    return [
                        'transaction_id' => $payment->midtrans_transaction_id,
                        'order_id' => $payment->order_id,
                        'amount' => (float) $payment->amount,
                    ];
                })
                ->toArray();
        }

        return collect($result['data']['transactions'] ?? [])
            ->filter(function ($trx) {
                return in_array($trx['transaction_status'] ?? '', ['settlement', 'capture']);
            })
            ->map(function ($trx) {
                return [
                    'transaction_id' => $trx['transaction_id'],
                    'order_id' => $trx['order_id'],
                    'amount' => (float) $trx['gross_amount'],
                ];
            })
            ->toArray();
    }

    /**
     * Get system payments for reconciliation
     */
    protected function getSystemPayments(string $startDate, string $endDate): Collection
    {
        return Payment::where(function ($query) use ($startDate, $endDate) {
                $query->whereDate('created_at', '>=', $startDate)
                      ->whereDate('created_at', '<=', $endDate);
            })
            ->orWhere(function ($query) use ($startDate, $endDate) {
                $query->whereDate('paid_at', '>=', $startDate)
                      ->whereDate('paid_at', '<=', $endDate);
            })
            ->get();
    }

    /**
     * Get reconciliation summary report
     */
    public function getSummaryReport(string $startDate, string $endDate): array
    {
        $reconciliations = PaymentReconciliation::whereBetween('reconciliation_date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->get();

        return [
            'total_reconciliations' => $reconciliations->count(),
            'total_transactions' => $reconciliations->sum('total_transactions'),
            'total_matched' => $reconciliations->sum('matched_transactions'),
            'total_unmatched' => $reconciliations->sum('unmatched_transactions'),
            'total_amount' => $reconciliations->sum('total_amount'),
            'matched_amount' => $reconciliations->sum('matched_amount'),
            'unmatched_amount' => $reconciliations->sum('unmatched_amount'),
            'average_match_rate' => $reconciliations->avg('match_rate'),
            'by_date' => $reconciliations->map(function ($rec) {
                return [
                    'date' => $rec->reconciliation_date->format('Y-m-d'),
                    'match_rate' => $rec->match_rate,
                    'total_transactions' => $rec->total_transactions,
                    'total_amount' => $rec->total_amount,
                ];
            }),
        ];
    }

    /**
     * Resolve unmatched item manually
     */
    public function resolveUnmatchedItem(int $itemId, ?int $paymentId, string $resolution): bool
    {
        $item = ReconciliationItem::findOrFail($itemId);
        
        if ($paymentId) {
            $payment = Payment::findOrFail($paymentId);
            
            $item->update([
                'payment_id' => $paymentId,
                'system_amount' => $payment->amount,
                'match_status' => 'matched',
                'notes' => "Manually resolved: {$resolution}",
            ]);

            // Update reconciliation counters
            $reconciliation = $item->reconciliation;
            $reconciliation->increment('matched_transactions');
            $reconciliation->decrement('unmatched_transactions');
            $reconciliation->increment('matched_amount', $item->midtrans_amount);
            $reconciliation->decrement('unmatched_amount', $item->midtrans_amount);

            return true;
        }

        $item->update([
            'notes' => "Manual resolution: {$resolution}",
        ]);

        return true;
    }
}

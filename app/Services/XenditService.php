<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\Payment;
use App\Models\Notification;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class XenditService
{
    protected string $secretKey;
    protected string $publicKey;
    protected string $apiUrl;
    protected bool $isProduction;

    public function __construct()
    {
        $this->secretKey = config('xendit.secret_key');
        $this->publicKey = config('xendit.public_key');
        $this->apiUrl = config('xendit.api_url');
        $this->isProduction = config('xendit.is_production');
    }

    /**
     * Create Invoice for payment
     */
    public function createInvoice(Bill $bill, Payment $payment): array
    {
        try {
            $invoiceDuration = (int) config('xendit.invoice_duration', 86400);
            
            $params = [
                'external_id' => $payment->order_id,
                'amount' => (int) $payment->amount,
                'payer_email' => $payment->user->email,
                'description' => $bill->billType->name . ' - ' . $bill->student->name,
                'invoice_duration' => $invoiceDuration,
                'customer' => [
                    'given_names' => $bill->student->name,
                    'email' => $payment->user->email,
                    'mobile_number' => $payment->user->phone ?? null,
                ],
                'customer_notification_preference' => [
                    'invoice_created' => ['email'],
                    'invoice_reminder' => ['email'],
                    'invoice_paid' => ['email'],
                ],
                'success_redirect_url' => config('xendit.success_redirect_url') ?? route('payment.finish'),
                'failure_redirect_url' => config('xendit.failure_redirect_url') ?? route('payment.error'),
                'currency' => 'IDR',
                'items' => [
                    [
                        'name' => substr($bill->billType->name . ' - ' . $bill->student->name, 0, 256),
                        'quantity' => 1,
                        'price' => (int) $payment->amount,
                    ],
                ],
            ];

            $response = Http::withBasicAuth($this->secretKey, '')
                ->post($this->apiUrl . '/v2/invoices', $params);

            if ($response->successful()) {
                $data = $response->json();
                
                $payment->update([
                    'xendit_invoice_id' => $data['id'],
                    'xendit_invoice_url' => $data['invoice_url'],
                    'expired_at' => now()->addSeconds($invoiceDuration),
                ]);

                return [
                    'success' => true,
                    'invoice_id' => $data['id'],
                    'invoice_url' => $data['invoice_url'],
                    'expiry_date' => $data['expiry_date'],
                ];
            }

            Log::error('Xendit Invoice Error', [
                'response' => $response->json(),
                'payment_id' => $payment->id,
            ]);

            return [
                'success' => false,
                'message' => 'Gagal membuat invoice pembayaran',
            ];

        } catch (Exception $e) {
            Log::error('Xendit Exception', [
                'message' => $e->getMessage(),
                'payment_id' => $payment->id,
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem',
            ];
        }
    }

    /**
     * Create Virtual Account for payment
     */
    public function createVirtualAccount(Bill $bill, Payment $payment, string $bankCode = 'BNI'): array
    {
        try {
            $paymentExpiry = (int) config('xendit.payment_expiry', 1440);
            
            $params = [
                'external_id' => $payment->order_id,
                'bank_code' => $bankCode,
                'name' => $bill->student->name,
                'expected_amount' => (int) $payment->amount,
                'is_closed' => true,
                'is_single_use' => true,
                'expiration_date' => now()->addMinutes($paymentExpiry)->toIso8601String(),
            ];

            $response = Http::withBasicAuth($this->secretKey, '')
                ->post($this->apiUrl . '/callback_virtual_accounts', $params);

            if ($response->successful()) {
                $data = $response->json();
                
                $payment->update([
                    'xendit_va_id' => $data['id'],
                    'va_number' => $data['account_number'],
                    'va_bank' => $data['bank_code'],
                    'expired_at' => now()->addMinutes($paymentExpiry),
                ]);

                return [
                    'success' => true,
                    'va_id' => $data['id'],
                    'va_number' => $data['account_number'],
                    'bank_code' => $data['bank_code'],
                    'expected_amount' => $data['expected_amount'],
                    'expiration_date' => $data['expiration_date'],
                ];
            }

            Log::error('Xendit VA Error', [
                'response' => $response->json(),
                'payment_id' => $payment->id,
            ]);

            return [
                'success' => false,
                'message' => 'Gagal membuat Virtual Account',
            ];

        } catch (Exception $e) {
            Log::error('Xendit Exception', [
                'message' => $e->getMessage(),
                'payment_id' => $payment->id,
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem',
            ];
        }
    }

    /**
     * Get Invoice status
     */
    public function getInvoiceStatus(string $invoiceId): array
    {
        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->get($this->apiUrl . '/v2/invoices/' . $invoiceId);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'message' => 'Gagal mendapatkan status invoice',
            ];

        } catch (Exception $e) {
            Log::error('Xendit Get Invoice Exception', [
                'message' => $e->getMessage(),
                'invoice_id' => $invoiceId,
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem',
            ];
        }
    }

    /**
     * Handle webhook callback from Xendit
     */
    public function handleWebhook(array $payload): array
    {
        try {
            $externalId = $payload['external_id'] ?? null;
            $status = $payload['status'] ?? null;

            if (!$externalId) {
                return [
                    'success' => false,
                    'message' => 'External ID tidak ditemukan',
                ];
            }

            $payment = Payment::where('order_id', $externalId)->first();

            if (!$payment) {
                Log::warning('Xendit Webhook: Payment not found', [
                    'external_id' => $externalId,
                ]);

                return [
                    'success' => false,
                    'message' => 'Payment tidak ditemukan',
                ];
            }

            // Map Xendit status to our status
            $statusMapping = [
                'PAID' => 'success',
                'SETTLED' => 'success',
                'EXPIRED' => 'expired',
                'FAILED' => 'failed',
                'PENDING' => 'pending',
            ];

            $newStatus = $statusMapping[$status] ?? 'pending';

            $payment->update([
                'status' => $newStatus,
                'paid_at' => $newStatus === 'success' ? now() : null,
                'xendit_payment_id' => $payload['id'] ?? null,
                'payment_method' => $payload['payment_method'] ?? null,
                'payment_channel' => $payload['payment_channel'] ?? null,
            ]);

            // If payment successful, update bill
            if ($newStatus === 'success') {
                $bill = $payment->bill;
                $totalPaid = $bill->payments()->where('status', 'success')->sum('amount');
                
                if ($totalPaid >= $bill->amount) {
                    $bill->update(['status' => 'paid']);
                } else {
                    $bill->update(['status' => 'partial']);
                }

                // Create notification
                Notification::create([
                    'user_id' => $payment->user_id,
                    'type' => 'payment_success',
                    'title' => 'Pembayaran Berhasil',
                    'message' => 'Pembayaran untuk ' . $bill->billType->name . ' sebesar Rp ' . number_format($payment->amount, 0, ',', '.') . ' telah berhasil.',
                    'data' => [
                        'payment_id' => $payment->id,
                        'bill_id' => $bill->id,
                    ],
                ]);

                // Log activity
                ActivityLog::create([
                    'user_id' => $payment->user_id,
                    'action' => 'payment_success',
                    'description' => 'Pembayaran berhasil via Xendit',
                    'properties' => [
                        'payment_id' => $payment->id,
                        'amount' => $payment->amount,
                        'xendit_payment_id' => $payload['id'] ?? null,
                    ],
                ]);
            }

            return [
                'success' => true,
                'message' => 'Webhook processed successfully',
                'payment_status' => $newStatus,
            ];

        } catch (Exception $e) {
            Log::error('Xendit Webhook Exception', [
                'message' => $e->getMessage(),
                'payload' => $payload,
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem',
            ];
        }
    }

    /**
     * Verify webhook callback token
     */
    public function verifyWebhookToken(string $token): bool
    {
        $webhookToken = config('xendit.webhook_token');
        
        if (empty($webhookToken)) {
            // If no webhook token configured, skip verification (for testing)
            return true;
        }

        return hash_equals($webhookToken, $token);
    }

    /**
     * Get available payment methods
     */
    public function getAvailablePaymentMethods(): array
    {
        return [
            'virtual_account' => [
                'name' => 'Virtual Account',
                'banks' => config('xendit.va_bank_codes'),
            ],
            'invoice' => [
                'name' => 'Xendit Invoice',
                'description' => 'Berbagai metode pembayaran (VA, E-Wallet, Retail)',
            ],
        ];
    }

    /**
     * Get balance
     */
    public function getBalance(): array
    {
        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->get($this->apiUrl . '/balance');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'balance' => $response->json()['balance'],
                ];
            }

            return [
                'success' => false,
                'message' => 'Gagal mendapatkan saldo',
            ];

        } catch (Exception $e) {
            Log::error('Xendit Get Balance Exception', [
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem',
            ];
        }
    }
}

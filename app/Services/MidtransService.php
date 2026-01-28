<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\Payment;
use App\Models\Notification;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class MidtransService
{
    protected string $serverKey;
    protected string $clientKey;
    protected string $merchantId;
    protected bool $isProduction;
    protected string $apiUrl;

    public function __construct()
    {
        $this->serverKey = config('midtrans.server_key');
        $this->clientKey = config('midtrans.client_key');
        $this->merchantId = config('midtrans.merchant_id');
        $this->isProduction = config('midtrans.is_production');
        $this->apiUrl = config('midtrans.api_url');
    }

    /**
     * Create Snap Token for payment
     */
    public function createSnapToken(Bill $bill, Payment $payment): array
    {
        try {
            $params = [
                'transaction_details' => [
                    'order_id' => $payment->order_id,
                    'gross_amount' => (int) $payment->amount,
                ],
                'customer_details' => [
                    'first_name' => $bill->student->name,
                    'email' => $payment->user->email,
                    'phone' => $payment->user->phone ?? '',
                ],
                'item_details' => [
                    [
                        'id' => $bill->invoice_number,
                        'price' => (int) $payment->amount,
                        'quantity' => 1,
                        'name' => substr($bill->billType->name . ' - ' . $bill->student->name, 0, 50),
                    ],
                ],
                'callbacks' => [
                    'finish' => route('payment.finish'),
                    'error' => route('payment.error'),
                    'pending' => route('payment.pending'),
                ],
                'expiry' => [
                    'start_time' => now()->format('Y-m-d H:i:s O'),
                    'unit' => 'minutes',
                    'duration' => config('midtrans.payment_expiry'),
                ],
                'enabled_payments' => config('midtrans.enabled_payments'),
            ];

            $response = Http::withBasicAuth($this->serverKey, '')
                ->post($this->apiUrl . '/snap/v1/transactions', $params);

            if ($response->successful()) {
                $data = $response->json();
                
                $payment->update([
                    'midtrans_order_id' => $payment->order_id,
                    'expired_at' => now()->addMinutes(config('midtrans.payment_expiry')),
                ]);

                return [
                    'success' => true,
                    'snap_token' => $data['token'],
                    'redirect_url' => $data['redirect_url'],
                ];
            }

            Log::error('Midtrans Snap Token Error', [
                'response' => $response->json(),
                'payment_id' => $payment->id,
            ]);

            return [
                'success' => false,
                'message' => 'Gagal membuat transaksi pembayaran',
            ];

        } catch (Exception $e) {
            Log::error('Midtrans Exception', [
                'message' => $e->getMessage(),
                'payment_id' => $payment->id,
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Handle notification from Midtrans
     */
    public function handleNotification(array $notification): array
    {
        try {
            $orderId = $notification['order_id'];
            $transactionStatus = $notification['transaction_status'];
            $fraudStatus = $notification['fraud_status'] ?? null;
            $paymentType = $notification['payment_type'] ?? null;

            $payment = Payment::where('order_id', $orderId)->first();

            if (!$payment) {
                Log::warning('Payment not found for notification', ['order_id' => $orderId]);
                return ['success' => false, 'message' => 'Payment not found'];
            }

            // Verify signature
            $signatureKey = hash('sha512', 
                $orderId . 
                $notification['status_code'] . 
                $notification['gross_amount'] . 
                $this->serverKey
            );

            if ($signatureKey !== $notification['signature_key']) {
                Log::warning('Invalid signature', ['order_id' => $orderId]);
                return ['success' => false, 'message' => 'Invalid signature'];
            }

            $payment->update([
                'midtrans_transaction_id' => $notification['transaction_id'] ?? null,
                'midtrans_response' => $notification,
                'payment_method' => $paymentType,
                'payment_channel' => $this->getPaymentChannel($notification),
            ]);

            // Process based on transaction status
            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'accept') {
                    $this->handleSuccessPayment($payment);
                } elseif ($fraudStatus == 'challenge') {
                    $payment->update(['status' => 'pending']);
                }
            } elseif ($transactionStatus == 'settlement') {
                $this->handleSuccessPayment($payment);
            } elseif (in_array($transactionStatus, ['cancel', 'deny'])) {
                $payment->markAsFailed();
            } elseif ($transactionStatus == 'expire') {
                $payment->markAsExpired();
            } elseif ($transactionStatus == 'pending') {
                $payment->update(['status' => 'pending']);
            }

            return ['success' => true, 'message' => 'Notification processed'];

        } catch (Exception $e) {
            Log::error('Midtrans Notification Error', [
                'message' => $e->getMessage(),
                'notification' => $notification,
            ]);

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Handle successful payment
     */
    protected function handleSuccessPayment(Payment $payment): void
    {
        $payment->markAsSuccess();
        
        $bill = $payment->bill;
        $bill->updatePaymentStatus();

        // Send notification to parent
        Notification::send(
            $payment->user_id,
            'Pembayaran Berhasil',
            "Pembayaran untuk {$bill->billType->name} sebesar {$payment->formatted_amount} telah berhasil.",
            'success',
            route('parent.payments.show', $payment->id)
        );

        // Log activity
        ActivityLog::log(
            'payment_success',
            'payments',
            "Pembayaran {$payment->order_id} berhasil untuk tagihan {$bill->invoice_number}"
        );
    }

    /**
     * Get payment channel from notification
     */
    protected function getPaymentChannel(array $notification): ?string
    {
        if (isset($notification['va_numbers'][0]['bank'])) {
            return $notification['va_numbers'][0]['bank'] . '_va';
        }
        
        if (isset($notification['bank'])) {
            return $notification['bank'];
        }

        return $notification['payment_type'] ?? null;
    }

    /**
     * Check transaction status
     */
    public function checkStatus(string $orderId): array
    {
        try {
            $response = Http::withBasicAuth($this->serverKey, '')
                ->get($this->apiUrl . '/v2/' . $orderId . '/status');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to check status',
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get Midtrans transactions for reconciliation
     */
    public function getTransactions(string $fromDate, string $toDate, int $page = 1): array
    {
        try {
            $response = Http::withBasicAuth($this->serverKey, '')
                ->get($this->apiUrl . '/v2/transactions', [
                    'from' => $fromDate,
                    'to' => $toDate,
                    'page' => $page,
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to fetch transactions',
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get client key for frontend
     */
    public function getClientKey(): string
    {
        return $this->clientKey;
    }

    /**
     * Get snap URL
     */
    public function getSnapUrl(): string
    {
        return config('midtrans.snap_url');
    }
}

<?php

namespace App\Http\Controllers;

use App\Services\MidtransService;
use App\Services\XenditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    protected MidtransService $midtransService;
    protected XenditService $xenditService;

    public function __construct(MidtransService $midtransService, XenditService $xenditService)
    {
        $this->midtransService = $midtransService;
        $this->xenditService = $xenditService;
    }

    public function midtrans(Request $request)
    {
        Log::info('Midtrans Webhook Received', $request->all());

        $notification = $request->all();

        $result = $this->midtransService->handleNotification($notification);

        if ($result['success']) {
            return response()->json(['status' => 'ok']);
        }

        return response()->json(['status' => 'error', 'message' => $result['message']], 400);
    }

    public function xendit(Request $request)
    {
        Log::info('Xendit Webhook Received', $request->all());

        // Verify webhook token
        $webhookToken = $request->header('x-callback-token');
        if (!$this->xenditService->verifyWebhookToken($webhookToken ?? '')) {
            Log::warning('Xendit Webhook: Invalid token');
            return response()->json(['status' => 'error', 'message' => 'Invalid token'], 401);
        }

        $payload = $request->all();

        $result = $this->xenditService->handleWebhook($payload);

        if ($result['success']) {
            return response()->json(['status' => 'ok']);
        }

        return response()->json(['status' => 'error', 'message' => $result['message']], 400);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WhatsAppWebhookController extends Controller
{
    //
    public function verify(Request $request)
    {
        $verifyToken = env('WHATSAPP_VERIFY_TOKEN');

        if (
            $request->get('hub_mode') === 'subscribe' &&
            $request->get('hub_verify_token') === $verifyToken
        ) {
            return response($request->get('hub_challenge'), 200);
        }

        return response('Invalid verify token', 403);
    }

    public function receive(Request $request)
    {
         $rawPayload = $request->getContent();
        \Log::info('WhatsApp Webhook Raw', [
        'payload' => $rawPayload
        ]);

        return response()->json([
            'status' => 'received'
        ], 200);
    }
}

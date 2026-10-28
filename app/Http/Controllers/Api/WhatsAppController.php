<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use DB;

class WhatsAppController extends Controller
{
    public function send($name,$machineId)
    {
        //exit();
        $phoneNumberId = env('WHATSAPP_PHONE_NUMBER_ID');
        $accessToken = env('WHATSAPP_ACCESS_TOKEN');
        $m_name=$name.'('.$machineId.')';
        $e_name=DB::table('employees')->where('designation_id',4)->get();
        foreach($e_name as $e_name_row)
        {
        $response = Http::withToken($accessToken)
            ->post(
                "https://graph.facebook.com/v25.0/{$phoneNumberId}/messages",
                [
                    'messaging_product' => 'whatsapp',

                    'to' => '91'.$e_name_row->mobile,

                    'type' => 'template',

                    'template' => [
                        'name' => 'mes_eng',

                        'language' => [
                            'code' => 'en'
                        ],

                        'components' => [
                            [
                                'type' => 'body',

                                'parameters' => [
                                    [
                                        'type' => 'text',
                                        'parameter_name' => 'machine_id',
                                        'text' => $m_name
                                    ]
                                    
                                ]
                            ]
                        ]
                    ]
                ]
            );
        }
        return response()->json([
            'status' => $response->successful(),
            'response' => $response->json()
        ], $response->status());
    }
}

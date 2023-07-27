<?php

namespace App\Help;

use illuminate\Support\Facades\Http;

class help
{
    public static function postbyOrder($params){
            # code...
            $url = env( 'URL_SERVICE_ORDER').'api/orders';

            try {
                $response = Http::post($url, $params);
                $data = $response->json();
                $data['http_code'] = $response->getStatusCode();
                return $data;

                } catch (\Throwable $th)
                    {
                //throw $th;
                        return [
                            'status' => 'error',
                            'http_code' => 500,
                            'message' => 'Service Order Payment Unavailable'
                        ];
                    }
    }
}




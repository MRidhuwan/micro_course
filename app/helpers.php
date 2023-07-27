<?php
use Illuminate\Support\Facades\Http;

function getUser($userId){

    $url = env("SERVICE_USER_URL").'users/'.$userId ;

    try {
        //code...
        $response = Http::timeout(10)->get($url);
        $data = $response->json();
        $data['http_code'] = $response->getStatusCode();

        return $data;

    } catch (\Throwable $th) {
        //throw $th;
        return [
            'status' => 'error',
            'http_code' => 500,
            'message' => 'Service User Unavailable',
        ];
    }
}

function getUserById($userIds = [])
{
    $url = env("SERVICE_USER_URL").'users/';

    try {
        if (count($userIds) === 0 ) {
            return [
            'status' => 'success',
            'http_code' => 200,
            'data' => [],
            ];
        }
        $response = Http::timeout(10)->get($url, ['$userIds[]' =>$userIds]);
        $data = $response->json();
        $data['http_code'] = $response->getStatusCode();
        return $data;

    } catch (\Throwable $th) {
        return [
            'status' => 'error',
            'http_code' => 500,
            'message' => 'Service User Unavailable',
        ];
    }

    // if (! function_exists('postbyOrder')){
    //     # code...
    //     function postbyOrder($params)
    //     {
    //     # code...
    //     $url = env( 'URL_SERVICE_ORDER').'api/orders';

    //     try {
    //         $response = Http::post($url, $params);
    //         $data = $response->json();
    //         $data['http_code'] = $response->getStatusCode();
    //         return $data;

    //         } catch (\Throwable $th)
    //             {

    //                 return [
    //                     'status' => 'error',
    //                     'http_code' => 500,
    //                     'message' => 'Service Order Payment Unavailable'
    //                 ];
    //             }
    //     }
    // }
}

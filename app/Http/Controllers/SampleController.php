<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SampleController extends Controller
{
    public function getUserList(Request $request) {
        $response = [];
     
        $accessToken = env('SLACK_ACCESS_TOKEN', null);
        if (empty($accessToken)) {
            $response["message"] = "tokenエラー";
            return new JsonResponse($response, 500);
        }
     
        $base = "https://slack.com/api/users.list";
        //下記トークンはクエリパラメタに追加するもの。最新のAPIでは使用不可
        //$param = "?token=${accessToken}";
        
        // $url = $base . $param;
        $url = $base;
        // $headers = [ "Content-Type: application/x-www-form-urlencoded,application/json" ];
        $headers = [ "Content-Type: application/x-www-form-urlencoded,application/json",
                     "Authorization: Bearer " . $accessToken];
        // privateメソッドにリクエスト処理を書いていく
        $result = self::request($url, "GET", null, $headers);
     
        if ($result["status_code"] !== 200) {
            $response["message"] = "通信エラー";
            return new JsonResponse($response, 500);
        }
     
        $response["body"] = $result["body"];
        $response["message"] = "success";
        // return new JsonResponse($response);
        return new JsonResponse($result);
     }

     private static function request($url, $method, $body, $headers) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_HEADER, true);
     
        if (!empty($body)) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        }
     
        $responseJsonText = curl_exec($curl);
        $body = json_decode($responseJsonText , true);
     
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl); // curlの処理終わり
     
        $result = [];
        $result['status_code'] = $httpCode;
        $result['body'] = $body;
        return $result;
     }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SampleController extends Controller
{
    /**
     * curlでslackapi呼び出し
     */
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

    /**
     * リクエストの生成メソッド
     */
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

    /**
     * Guzzleでslackapiを呼び出す
     */
    public function with_headers() {
        $base_url = 'https://slack.com/api';
        $client = new \GuzzleHttp\Client( [
            'base_uri' => $base_url,
        ] );
    
        $path = '/users.list';
        $headers = [
            'Origin'                    => 'https://slack.com/api',
            'Accept-Encoding'           => 'gzip, deflate, br',
            'Accept-Language'           => 'ja,en-US;q=0.8,en;q=0.6',
            'Upgrade-Insecure-Requests' => '1',
            'User-Agent'                => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.71 Safari/537.36',
            'Content-Type'              => 'application/x-www-form-urlencoded',
            'Accept'                    => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Cache-Control'             => 'max-age=0',
            'Referer'                   => 'https://slack.com/api',
            'Connection'                => 'keep-alive',
            'Authorization'             => 'Bearer ' . env('SLACK_ACCESS_TOKEN', null),
        ];
        $response = $client->request( 'GET', $path,
            [
                'allow_redirects' => true,
                'headers'         => $headers,
                //'form_params'     => $form_params,
            ] );
        $response_body = (string) $response->getBody();
        echo $response_body;
    }

}
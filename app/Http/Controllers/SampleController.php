<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SampleController extends Controller
{



    /**
     * SlackApiの呼び出し
     * 
     * @var String $path 呼び出すSlack Method
     * @var Array $form_params Slack Methodに渡すパラメータ（クエリ文字列）
     * not_in_channelが表示されたらチャンネルにアプリを追加する必要あり！
     */
    public function callSlackApi($path, $form_params) {
        $base_url = 'https://slack.com/api';
        $client = new \GuzzleHttp\Client( [
            'base_uri'  => $base_url,
            'verify'    => false,
        ] );
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
        // dd($path);
        $response = $client->request( 'GET', $base_url . $path, [
                'allow_redirects' => true,
                'headers'         => $headers,
                'verify'          => false,
                'query'           => $form_params,
        ]);
        $response_body = (string) $response->getBody();
        // dd($response_body);
        // echo $response_body;
        echo json_encode($response_body, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }

    /**
     * ユーザ一覧取得
     */
    public function with_headers() {
        $path           = '/users.list';    // 呼び出すSlack Method
        $form_params    = null;             // Slack Methodに渡すパラメータ
        $this->callSlackApi($path, null);
    }

    /**
     * チャンネルの投稿を取得
     */
    public function getChannelHistory() {
        $path           = '/conversations.history';     // 呼び出すSlack Method
        $form_params    = ['channel' => 'C013STP3QDV']; // Slack Methodに渡すパラメータ
        $this->callSlackApi($path, $form_params);

    }




/*
|--------------------------------------------------------------------------
| 以下Guzzleを使用しないメソッド
|--------------------------------------------------------------------------
|
| これより下はcurlでSlackメソッドを呼び出し。
| 上記callSlackApiメソッドでGuzzle経由で呼び出せるので、以下使用する必要なしだが
| 記録として残しておく。
|
*/
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


}
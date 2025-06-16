<?php

namespace App\Helpers;

use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Psr\Http\Message\ResponseInterface;

class SurmHelper
{
    protected Client $client;
    protected ResponseInterface $response;
    protected static SurmHelper $instance;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => config('hd.surm.endpoint'),
        ]);
    }

    public static function init(): SurmHelper
    {
        if (!isset(self::$instance)) {
            self::$instance = new SurmHelper();
        }
        return self::$instance;
    }

    protected function makeRequest(
        string $method,
        string $endpoint,
        array  $headers = [],
        array  $json = [])
    {
        try {
            $data = [
                'headers' => [
                    'Authorization' => 'Bearer ' . config('hd.surm.token'),
                ]
            ];
            if (!empty($headers)) {
                $data['headers'] = array_merge($headers, $data['headers']);
            }
            if (!empty($json)) {
                $data['json'] = $json;
            }
            $this->response = $this->client->request($method, $endpoint, $data);
        } catch (\Exception $exception) {
            dd($exception->getMessage());
        }

    }

    /**
     * @return string
     */
    public static function getWorkplacesByUser()
    {
        /** @var User $user */
        $user = Auth::user();
        self::init();
        $uri = 'workplaces/' . $user->username;
        self::$instance->makeRequest(method: 'GET', endpoint: $uri);
        return json_decode(self::$instance->response->getBody()->getContents());
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public static function addTicket(Request $request)
    {
        dd($request->all());
        /** @var User $user */
        $user = Auth::user();
        self::init();
        $uri = 'tickets';
        self::$instance->makeRequest(method: 'POST', endpoint: $uri);
        return json_decode(self::$instance->response->getBody()->getContents());
    }
}

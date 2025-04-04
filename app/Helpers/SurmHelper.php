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
        array  $headers = [])
    {
        try {
            $this->response = $this->client->request($method, $endpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . config('hd.surm.token'),
                ]
            ]);
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
        $uri = $user->username . '/workplaces';
        self::$instance->makeRequest(method: 'GET', endpoint: $uri);
        return self::$instance->response->getBody()->getContents();
    }
}

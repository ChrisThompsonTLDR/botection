<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cache;
use GuzzleHttp\Client;
use Log;

class OauthController extends Controller
{

    public function authorize_it()
    {
        $url = 'https://www.reddit.com/api/v1/authorize?' . http_build_query([
            'client_id'     => config('services.reddit.client_id'),
            'response_type' => 'code',
            'redirect_uri'  => route('oauth.callback'),
            'duration'      => 'permanent',
            'scope'         => 'read',
            'state'         => 'banana',
        ]);

        return redirect()->to($url);
    }

    public function callback(Request $request)
    {
        /*$provider = new RedditProvider([
            'clientId'                => 'lvXqzbPtqzM8Dw',    // The client ID assigned to you by the provider
            'clientSecret'            => 'KT6jUXuq4QH1QDBOU26i9GCbOTQ',   // The client password assigned to you by the provider
            'redirectUri'             => 'http://phplaravel-38433-442716.cloudwaysapps.com/callback',
            'urlAuthorize'            => 'https://www.reddit.com/api/v1/authorize',
            'urlAccessToken'          => 'https://www.reddit.com/api/v1/access_token',
            'urlResourceOwnerDetails' => null,
        ]);

        try {
            $accessToken = $provider->getAccessToken('authorization_code', [
                'code'         => $request->input('code'),
                'redirect_uri' => 'http://phplaravel-38433-442716.cloudwaysapps.com/callback',
                'headers' => [
                    'User-Agent' => 'laravel:jretoreddit:0.1, (by /u/loki_racer)',
                ],
            ]);
        } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
            \Log::error($e->getMessage(), [
                'line' => __LINE__,
                'file' => __file__,
            ]);
            dd($e->getMessage());
            exit;
        }*/

        $client = new Client([
            'base_uri' => 'https://www.reddit.com/api/v1/',
        ]);

        try {
            $response = $client->post('access_token', [
                'auth' => [config('services.reddit.client_id'), config('services.reddit.secret')],
                'headers' => [
                    'User-Agent' => 'laravel:' . config('app.name') . ':0.1, (by /u/loki_racer)',
                ],
                'form_params' => [
                    'grant_type'   => 'authorization_code',
                    'code'         => $request->input('code'),
                    'redirect_uri' => route('oauth.callback'),
                ]
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage(), [
                'line' => __LINE__,
                'file' => __FILE__,
            ]);
            dd($e->getMessage());
            exit;
        }

        $accessToken = json_decode((string) $response->getBody());

        $accessToken->expires_at = time() + $accessToken->expires_in;

        auth()->user()->token = $accessToken;

        auth()->user()->save();

        return redirect()->route('home')->with('success', 'Oauth token stored');
    }
}
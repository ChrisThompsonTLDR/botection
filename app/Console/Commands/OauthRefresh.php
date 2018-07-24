<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use Carbon\Carbon;
use Log;
use GuzzleHttp\Client;

class OauthRefresh extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'oauth:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'refresh the oauth token';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $users = User::where('expires_at', '<', Carbon::now()->toDatetimeString())->get();

        foreach ($users as $user) {
            $accessToken = $user->token;

            if ($accessToken->expires_at < time()) {
                $client = new Client([
                    'base_uri' => 'https://www.reddit.com/api/v1/',
                    'auth' => [config('services.reddit.client_id'), config('services.reddit.secret')],
                    'headers' => [
                        'User-Agent' => 'laravel:' . config('app.name') . ':0.1, (by /u/loki_racer)',
                    ],
                ]);

                try {
                    $response = $client->post('access_token', [
                        'form_params' => [
                            'grant_type'    => 'refresh_token',
                            'refresh_token' => $accessToken->refresh_token,
                        ]
                    ]);
                } catch (Exception $e) {
                    Log::error($e->getMessage(), [
                        'line' => __LINE__,
                        'file' => __FILE__,
                    ]);
                    exit;
                }

                $newToken = json_decode((string) $response->getBody());

                $newToken->expires_at    = time() + $newToken->expires_in;
                $newToken->refresh_token = $accessToken->refresh_token;

                $user->token = $newToken;

                $user->save();
            }
        }
    }
}

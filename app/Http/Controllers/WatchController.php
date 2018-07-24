<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Watch;
use Artisan;

class WatchController extends Controller
{

    public function store(Request $request)
    {
        $request->validate([
            'url' => 'url|required',
        ]);

        $pieces = parse_url($request->input('url'));

        $parts = explode('/', $pieces['path']);

        $watch            = new Watch;
        $watch->user      = auth()->user();
        $watch->url       = strtolower($request->input('url'));
        $watch->subreddit = strtolower($parts[2]);
        $watch->reddit_id = strtolower($parts[4]);
        $watch->slug      = isset($parts[5]) ? strtolower($parts[5]) : '';

        if (!$watch->save()) {
            return back()
                ->with('error', 'Failed to save thread to watch.');
        }

        Artisan::queue('reddit:watch');

        return back()
            ->with('success', 'This thread will now be watched for 4 hours.');
    }
}

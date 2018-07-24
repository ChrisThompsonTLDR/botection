<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Comment;

class PageController extends Controller
{
    public function home()
    {
        return view('home');
    }

    public function sub($subreddit)
    {
        $threads = Comment::where('subreddit', $subreddit)
                          ->where('score', '!=', null)
                          ->where('parent_id', null)
                          ->orderBy('created_at', 'desc')
                          ->paginate(100);

        return view('sub', compact('subreddit', 'threads'));
    }

    public function thread($subreddit, $id)
    {
        $thread = Comment::where('subreddit', $subreddit)
                         ->where('id', $id)
                         ->with(['user', 'children'])
                         ->orderBy('created_at', 'desc')
                         ->first();

        $subreddits = Comment::whereIn('author', $thread->authors->pluck('username'))
            ->where('subreddit', '!=', $subreddit)
            ->groupBy(['author', 'subreddit'])
            ->pluck('subreddit')
            ->groupBy(function ($item) {
                return $item;
            })
            ->map(function ($items, $key) {
                return $items->count();
            })
            ->toArray();

        $subreddits = array_sort($subreddits);

        arsort($subreddits);

        return view('thread', compact('thread', 'subreddits', 'authors'));
    }
}

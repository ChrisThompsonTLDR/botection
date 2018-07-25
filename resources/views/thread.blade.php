@extends ('layouts.app')

@section ('content')
<!-- As a link -->
<nav class="navbar navbar-light bg-light">
    <a class="navbar-brand" href="{{ route('home') }}">{{ config('app.name') }}</a>
    A tool for tracking down possible bots and bridaging.
</nav>
<div class="container-fluid">
    <div class="row">
        <div class="col">
            <nav aria-label="breadcrumb" class="my-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('sub', $thread->subreddit) }}">{{ $thread->subreddit }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $thread->title }}</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row mb-5">
        <div class="col">
            <div class="card bg-info text-white text-center">
                <div class="card-body">
                    <h3 class="card-title">{{ $thread->score }}</h3>
                    score
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-info text-white text-center">
                <div class="card-body">
                    <h3 class="card-title">@if ($thread->num_comments != 0){{ number_format($thread->score / $thread->num_comments, 2) }}@else{{ '--' }}@endif</h3>
                    score / comments
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-info text-white text-center">
                <div class="card-body">
                    <h3 class="card-title">{{ $thread->num_comments }}</h3>
                    comments
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-info text-white text-center">
                <div class="card-body">
                    <h3 class="card-title">{{ $thread->view_count or '--' }}</h3>
                    views
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-info text-white text-center">
                <div class="card-body">
                    <h3 class="card-title">{{ number_format(round($thread->descendants->avg('seconds_to_respond'))) }} seconds</h3>
                    average time to respond
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div>
                @include ('blocks.sparkline', ['comment' => $thread])

                {{ $thread->title }}
                <small class="pl-2"><a href="https://www.reddit.com/r/{{ $thread->subreddit }}/comments/{{ $thread->id }}" target="_blank"><i class="fal fa-external-link"></i></a></small>
            </div>
            <div class="small text-muted">
                <span data-toggle="tooltip" data-placement="top" title="{{ $thread->created_at->format('D M j, Y H:i T') }}">submitted {{ $thread->created_at->diffForHumans() }}</span> by {{ $thread->author }}
            </div>

            @if ($thread->body != '[deleted]')
                @parsedown ($thread->body)
            @else
                @if ($thread->history->firstWhere('body', '!=', '[deleted]'))
                    @parsedown ($thread->history->firstWhere('body', '!=', '[deleted]')->body)
                @else
                    [deleted]
                @endif
            @endif
            @if (!$thread->body)
            <code class="d-block">
                {{ $thread->url }}
            </code>
            @endif

            @foreach ($thread->children->sortBy('created_at') as $comment)
                @include ('comment', ['bg' => true, 'first' => true])
            @endforeach
        </div>
        <div class="col-md-3">
            <div class="card bg-light w-100 mb-5">
                <div class="card-body">
                    <h5 class="card-title">Subs from users in this thread</h5>
                    <div class="card-text">
                        @forelse ($subreddits as $sub => $count)
                            @if (round($count / $thread->authors->count() * 100) < 1)
                                @continue
                            @endif
                        <small>{{ $sub }}</small><div class="float-right small">{{ round($count / $thread->authors->count() * 100) }}%</div>
                        <div class="progress mb-2" style="height: 1px;">
                            <div class="progress-bar" role="progressbar" style="width: {{ round($count / $thread->authors->count() * 100) }}%;" aria-valuenow="{{ round($count / $thread->authors->count() * 100) }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        @empty
                        <p>No data available.</p>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="card bg-light w-100">
                <div class="card-body">
                    <h5 class="card-title">Notes</h5>
                    <div class="card-text">
                        <ul class="pl-3">
                            <li>vote counts are "fuzzed" by Reddit, so don't be surprised to see a little up/down movement in the vote counts</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
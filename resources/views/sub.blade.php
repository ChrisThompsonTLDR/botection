@extends ('layouts.app')

@section ('content')

<nav class="navbar navbar-light bg-light mb-5">
    <a class="navbar-brand" href="{{ route('home') }}">{{ config('app.name') }}</a>
    A tool for tracking down possible bots and bridaging.
</nav>
@include ('blocks.alerts')
<div class="container">
    <div class="row">
        <div class="col">
            <p class="lead mb-4">/r/{{ $subreddit }}</p>
        </div>
        {{--<div class="col">
            {{ Form::open(['route' => 'watch.store']) }}
                <div class="form-row align-items-center mt-2">
                    <div class="col">
                        {{ Form::text('url', null, ['class' => 'form-control', 'placeholder' => 'reddit thread url']) }}
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">Watch</button>
                    </div>
                </div>
            {{ Form::close() }}
        </div>--}}
    </div>
    @foreach ($threads as $thread)
    @php
    $muted = '';
    if ($thread->author == '[deleted]') {$muted = 'class="text-deleted"';}
    @endphp
    <div class="row mb-2 pb-2 border-bottom">
        <div class="col-sm-1">
            <i class="fas fa-arrow-alt-up"></i>
            {{ $thread->score }}
        </div>
        <div class="col-sm-1">
            <i class="fal fa-comment-alt"></i>
            {{ $thread->num_comments }}
        </div>
        <div class="col">
            <a href="{{ route('thread', [$thread->subreddit, $thread->id]) }}" {!! $muted !!}>{{ $thread->title }}</a>

            <small class="pl-2"><a href="https://www.reddit.com/r/{{ $thread->subreddit }}/comments/{{ $thread->id }}" target="_blank" {!! $muted !!}><i class="fal fa-external-link"></i></a></small>

            <div {!! $muted !!}>
                <span class="small text-muted">
                    <span data-toggle="tooltip" data-placement="top" title="{{ $thread->created_at->format('D M j, Y H:i T') }}">submitted {{ $thread->created_at->diffForHumans() }}</span> by {{ $thread->author }}
                </span>
            </div>
        </div>
    </div>
    @endforeach

    <div class="row">
        <div class="col">
            {{ $threads->links() }}
        </div>
    </div>


<?php /*
            <table class="table">
                <thead>
                <tr>
                    <th>Score</th>
                    <th nowrap="nowrap">Vote Ratio</th>
                    <th>Comments</th>
                    <th>Views</th>
                    <th></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($threads as $thread)
                    @php
                    $muted = '';
                    if ($thread->author == '[deleted]') {$muted = 'class="text-deleted"';}
                    @endphp
                    <tr>
                        <td {!! $muted !!}></td>
                        <td {!! $muted !!}>{{ $thread->upvote_ratio }}</td>
                        <td {!! $muted !!}>{{ $thread->num_comments }}</td>
                        <td {!! $muted !!}>{{ $thread->view_count }}</td>
                        <td {!! $muted !!}>
                            @if ($thread->removed)
                                <i class="fal fa-times-circle" data-toggle="tooltip" data-placement="top" title="removed by moderator"></i>
                            @endif
                            @if ($thread->watches->count() > 0)
                                <i class="fal fa-glasses" data-toggle="tooltip" data-placement="top" title="currently being watched"></i>
                            @endif
                            </td>
                        <td {!! $muted !!}>
                            <div>
                                <a href="{{ route('thread', [$thread->subreddit, $thread->id]) }}" {!! $muted !!}>{{ $thread->title }}</a>
                                <small class="pl-2"><a href="https://www.reddit.com/r/{{ $thread->subreddit }}/comments/{{ $thread->id }}" target="_blank" {!! $muted !!}><i class="fal fa-external-link"></i></a></small>
                            </div>
                            <div class="small text-muted">
                                <span data-toggle="tooltip" data-placement="top" title="{{ $thread->created_at->format('D M j, Y H:i T') }}">submitted {{ $thread->created_at->diffForHumans() }}</span> by {{ $thread->author }}
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $threads->links() }}
        </div>
    </div>*/ ?>
</div>
@endsection
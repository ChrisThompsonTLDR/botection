@extends('layouts.app')

@section('content')
<nav class="navbar navbar-light bg-light mb-5">
    <a class="navbar-brand" href="{{ route('home') }}">{{ config('app.name') }}</a>
    A tool for tracking down possible bots and bridaging.
</nav>
<div class="container">
    <div class="row">
        <div class="col"><h3 class="mb-5">Subreddits currently being monitored</h3></div>
    </div>
    <div class="row">
        @foreach (config('app.subreddits') as $sub)
        <div class="col-md-4">
            <div class="card bg-info text-white text-center mb-5">
                <div class="card-body">
                    <h3 class="card-title"><a href="{{ route('sub', $sub) }}" class="text-white">/r/{{ $sub }}</a></h3>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

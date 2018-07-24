@php
$muted = '';
if ($comment->author == '[deleted]') {$muted = 'text-deleted';}
@endphp
<div class="row {{ $muted }} @if (!isset($first)){{ 'ml-2' }}@else{{ 'ml-1' }}@endif mt-2 @if ($bg){{ 'bg-light' }}@else{{ 'bg-white' }}@endif">
    <div class="col">
        <div class="pl-1 py-1">
            <div class="row">
                <div class="col" style="max-width: 40px;"><div class="text-center w-100 font-weight-bold pt-2">{{ $comment->score }}</div></div>
                <div class="col">
                    @include ('blocks.sparkline')

                    <div class="small text-muted">
                        @if (!in_array($comment->author, ['[deleted]', '[removed]']))
                            {{ $comment->author }}
                        @elseif ($history = $comment->history->whereNotIn('author', ['[deleted]', '[removed]'])->first())
                            @parsedown ($history->author)
                        @else
                            @parsedown ($comment->author)
                        @endif

                        <span class="ml-2" data-toggle="tooltip" data-placement="top" title="{{ $comment->created_at->format('D M j, Y H:i T') }}">{{ $comment->created_at->diffForHumans() }}</span>
                        / <span data-toggle="tooltip" data-placement="top" title="{{ $comment->created_at->addSeconds($comment->seconds_to_respond)->format('D M j, Y H:i T') }}">{{ number_format($comment->seconds_to_respond) }} seconds to respond</span>
                        @if ($comment->user->seconds_to_respond)
                            / <span data-toggle="tooltip" data-placement="top" title="user usually takes {{ number_format($comment->user->seconds_to_respond) }} seconds to respond"><i class="fal fa-chevron-double-{{ (($comment->user->seconds_to_respond > $comment->seconds_to_respond) ? 'up' : 'down') }}"></i></span>
                        @else
                        / --
                        @endif

                        {{--<a href="https://www.reddit.com/r/{{ $comment->subreddit }}/comments/{{ $comment->id }}" target="_blank" class="ml-2 text-muted small"><i class="fal fa-external-link"></i></a>--}}
                        @if (!in_array($comment->author, ['[deleted]', '[removed]']) && $thread->author == $comment->author)
                            <span class="badge badge-primary" data-toggle="tooltip" data-placement="top" title="submitter">s</span>
                        @endif                            
                    </div>
                    <div>
                        @if (!in_array($comment->body, ['[deleted]', '[removed]']))
                            @parsedown ($comment->body)
                        @elseif ($history = $comment->history->whereNotIn('body', ['[deleted]', '[removed]'])->first())
                            @parsedown ($history->body)
                        @else
                            @parsedown ($comment->body)
                        @endif
                    </div>
                    @if ($comment->user)
                    <div class="small text-muted">
                        @if ($comment->user->hide_from_robots)
                            <span class="badge badge-light text-muted">hide_from_robots</span>
                        @endif
                        @if ($comment->user->is_gold)
                            <span class="badge badge-light text-muted">is_gold</span>
                        @endif
                        @if ($comment->user->is_mod)
                            <span class="badge badge-light text-muted">is_mod</span>
                        @endif
                        @if ($comment->user->verified)
                            <span class="badge badge-light text-muted">verified</span>
                        @endif
                        @if ($comment->user->has_verified_email)
                            <span class="badge badge-light text-muted">has_verified_email</span>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
            @foreach ($comment->children->sortBy('created_at') as $comment)
                @include ('comment', ['bg' => !$bg])
            @endforeach
        </div>
    </div>
</div>
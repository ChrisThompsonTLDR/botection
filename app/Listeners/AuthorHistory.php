<?php

namespace App\Listeners;

use App\Events\AuthorSaving;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AuthorHistory
{

    /**
     * Handle the event.
     *
     * @param  AuthorSaving  $event
     * @return void
     */
    public function handle(AuthorSaving $event)
    {
        //  do nothing if we have nothing
        if (empty($event->author->raw)) {
            return;
        }

        $event->author->history()->create(array_except($event->author->toArray(), ['id']));
    }
}

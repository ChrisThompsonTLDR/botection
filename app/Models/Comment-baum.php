<?php

namespace App;

use Baum\Node;
use App\Vote;
use App\Author;
use App\Watch;
use App\Events\CommentSaving;

class Comment extends Node
{

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

    protected $orderColumn = 'created_at';

    protected $dispatchesEvents = [
        'saving' => CommentSaving::class,
    ];

    protected $hidden = [
        'raw',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'edited_at',
        'approved_at',
    ];

    //  RELATIONSHIPS

    public function user()
    {
        return $this->belongsTo(Author::class, 'author', 'username');
    }

    public function history()
    {
        return $this->hasMany(CommentHistory::class);
    }

    public function watches()
    {
        return $this->hasMany(Watch::class, 'reddit_id', 'id');
    }


    //  MUTATORS

    public function setRawAttribute($field)
    {
        if (!is_string($field)) {
            $field = json_encode($field);
        }

        $this->attributes['raw'] = $field;
    }
}
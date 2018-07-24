<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Vote;
use App\Author;
use App\Watch;
use App\Events\CommentSaving;

class Comment extends Model
{

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

    protected $parent = 'parent_id';

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

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id', 'id')
                    ->with(['user', 'children']);
    }

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


    //  ACCESSORS

    public function getAuthorsAttribute($field)
    {
        return $this->children->authors()
            ->merge([$this->user])
            ->filter(function ($row) {
                return $row;
            })
            ->unique();
    }

    public function getDescendantsAttribute($field)
    {
        return $this->children->merge($this->children->descendants())
            ->filter(function ($row) {
                return $row;
            })
            ->unique();
    }
}
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\AuthorStat;
use App\AuthorHistory;
use App\Comment;
use App\Events\AuthorSaving;

class Author extends Model
{

    public $incrementing = false;

    protected $keyType = 'string';

    protected $primaryKey = 'username';

    protected $guarded = [];

    protected $dispatchesEvents = [
        'saving' => AuthorSaving::class,
    ];

    protected $hidden = [
        'raw',
    ];


    //  RELATIONSHIPS

    public function comments()
    {
        return $this->hasMany(Comment::class, 'author', 'username');
    }

    public function history()
    {
        return $this->hasMany(AuthorHistory::class, 'username', 'username');
    }

    public function stats()
    {
        return $this->hasMany(AuthorStat::class, 'username', 'author');
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
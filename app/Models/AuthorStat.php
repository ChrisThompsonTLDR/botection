<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Author;

class AuthorStat extends Model
{

    public $incrementing = false;

    protected $keyType = 'string';

    protected $primaryKey = 'reddit_id';

    protected $guarded = [];


    //  RELATIONSHIPS

    public function author()
    {
        return $this->belongsTo(Author::class, 'author', 'username');
    }
}
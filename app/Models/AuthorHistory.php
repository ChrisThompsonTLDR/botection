<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Author;

class AuthorHistory extends Model
{

    protected $table = 'author_history';

    protected $guarded = [];


    //  RELATIONSHIPS

    public function author()
    {
        return $this->belongsTo(Author::class);
    }
}
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Comment;

class CommentHistory extends Model
{

    protected $table = 'comment_history';

    protected $guarded = [];


    //  RELATIONSHIPS

    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }
}
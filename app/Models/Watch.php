<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Watch extends Model
{

    public $table = 'watch';

    //  RELATIONSHIPS

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    //  MUTATORS

    public function setUserAttribute(User $user)
    {
        $this->attributes['user_id'] = $user->id;
    }
}
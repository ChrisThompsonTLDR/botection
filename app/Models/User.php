<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Carbon\Carbon;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    //  ACCESSORS

    public function getTokenAttribute($field)
    {
        return json_decode($this->attributes['token']);
    }


    //  MUTATORS

    public function setTokenAttribute($field)
    {
        $this->attributes['token']      = json_encode($field);
        $this->attributes['expires_at'] = Carbon::createFromTimeStamp($field->expires_at)->toDatetimeString();
    }
}

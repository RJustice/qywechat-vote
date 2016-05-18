<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class QyVoteRole extends Model
{
    protected $fillable = ['userid','vid','status'];

    public function vuser(){
        return $this->belongsTo('App\QyUser','userid','userid');
    }
}

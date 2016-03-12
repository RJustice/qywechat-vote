<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QyVote extends Model
{
    protected $fillable = ['type','title','info','starttime','endtime','ym','status'];

    public function getNode(){
       return $this->hasMany('App\QyVoteNode','vid','id');
    }

    public function getVoteUser(){
        return $this->hasMany('App\QyVoteUser','vid','id');
    }

    public function getVoteRole(){
        return $this->hasMany('App\QyVoteRole','vid','id');
    }

}

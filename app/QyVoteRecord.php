<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QyVoteRecord extends Model
{
    //
    protected $fillable = ['vid','score','userid','ym','vuid','name','vnodeid','type','extra'];

    public function getVUser(){
        return $this->belongsTo('App\QyUser','vuid','userid');
    }

    public function getWhoVote(){
        return $this->belongsTo('App\QyUser','userid','userid');
    }

    public function node(){
        return $this->belongsTo('App\QyVoteNode','vnodeid','id');
    }

    public function vote(){
        return $this->belongsTo('App\QyVote','vid','id');
    }
    
}

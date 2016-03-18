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
    
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QyUser extends Model
{
    protected $fillable = ['userid','name','department','position','mobile','gender','email','weixinid','openid','avatar','status','extattr'];
    
}

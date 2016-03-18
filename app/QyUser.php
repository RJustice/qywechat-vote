<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QyUser extends Model
{
    protected $fillable = ['userid','name','department','position','mobile','gender','email','weixinid','openid','avatar','status','extattr'];
    
    public function getDepartment(){
        $depart = explode(',',trim($this->department,','));
        return QyGroup::whereIn('group_id',$depart);
    }
}

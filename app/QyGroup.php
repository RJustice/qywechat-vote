<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\QyUser;

class QyGroup extends Model
{
    protected $fillable = ['group_id','name','pid','order'];


    public function getMembers(){
        if( $this->pid == 0 ){
            return QyUser::all();
        }
        return QyUser::where('department','like','%,'.$this->group_id.',%')->get();
    }
}

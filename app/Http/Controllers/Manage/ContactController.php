<?php

namespace App\Http\Controllers\Manage;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Response,Input,Redirect;
use App\QyGroup;
use App\QyUser;


class ContactController extends Controller
{
    
    public function getGroupList(){
        $departments = QyGroup::all();
        $return = [
            'rs' => $departments->toArray(),
            'total' => $departments->count(),
        ];

        return Response::json($return);
    }

    public function getGroupMembers($id){
        $group = QyGroup::where('group_id',$id)->first();
        if( ! $group ){
            return Response::json(['rs'=>[],'total'=>0]);
        }
        $members = $group->getMembers();
        return Response::json(['rs'=>$members->toArray(),'total'=>$members->count()]);
    }

    public function getMemberInfo($id){
        $member = QyUser::where('userid',$id)->first();
        if( ! $member ){
            return Response::json(['rs'=>[],'total'=>0]);
        }
        return Response::json(['rs'=>$member,'total'=>1]);
    }
}

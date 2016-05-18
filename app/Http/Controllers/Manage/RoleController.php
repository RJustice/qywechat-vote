<?php

namespace App\Http\Controllers\Manage;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use Redirect;
use App\QyVoteRole;
use App\QyGroup;
use App\QyUser;
use DB;

class RoleController extends Controller
{
    public function index(){
        // $users = QyVoteRole::all();
        // return view('mrole.index',compact('users'));
        return $this->edit();
    }

    public function edit(){
        $roles = QyVoteRole::all();
        $users = [];
        foreach( $roles as $role ){
            $users[$role->userid] = [
                'department' => $role->vuser->department,
                'name' => $role->vuser->name,
                'position' => $role->vuser->position
            ];
        }
        return view('mrole.add',compact('users'));
    }

    public function update(Request $request){
        $roleuser = json_decode($request->input('formember'));
        foreach($roleuser as $userid=>$user){
            $data[] = [
                'userid'=>$userid
            ];
        }
        DB::table('qy_vote_roles')->truncate();
        QyVoteRole::insert($data);
        return redirect(url('/'));
    }

}

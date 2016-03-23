<?php

namespace App\Http\Controllers\Manage;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\QyGroup;
use App\QyUser;
use App\Classes\QyWechat;
use Response,Config,DB;

class SyncContactController extends Controller
{
    private $qyWechat = '';

    public function __construct(){
        $this->qyWechat = new QyWechat(Config::get('qywechat.vote'));
    }

    public function sync(){
        $departments = $this->qyWechat->getDepartment();
        if( $departments ){
            $data = [];
            foreach( $departments as $department ){
                $data[] = [
                    'group_id' => $department['id'],
                    'name' => $department['name'],
                    'pid' => $department['parentid'],
                    'order' => $department['order']
                ];
            }
            DB::transaction(function() use($data){
                DB::table('qy_groups')->delete();
                DB::table('qy_groups')->insert($data);
            });
        }

        $members = $this->qyWechat->getUserListInfo(1,1,'0');
        if( $members ){
            $mdata = [];
            foreach( $members as $member ){
                $mdata[] = [
                    'userid' => $member['userid'],
                    'name' => $member['name'],
                    'department' => ','.implode(',', $member['department']).',',
                    'position' => isset($member['position'])? $member['position']:'',
                    'mobile' => isset($member['mobile'])? $member['mobile'] : '',
                    'email' => isset($member['email'])? $member['email'] : '',
                    'gender' => isset($member['gender']) ? $member['gender'] : '',
                    'weixinid' => isset($member['weixinid']) ? $member['weixinid'] : '',
                    'avatar' => isset($member['avatar']) ? $member['avatar'] : '',
                    'status' => isset($member['status']) ? $member['status'] : '',
                ];
            }
            DB::transaction(function() use($mdata){
                DB::table('qy_users')->delete();
                DB::table('qy_users')->insert($mdata);
            });
            // return Response::json(['errcode'=>0,'rs'=>[],'total'=>0]);
            return redirect(url('manage/sync/rs'));
        }
    }

    public function syncRs(){
        return view('mvote.sync');
    }
}

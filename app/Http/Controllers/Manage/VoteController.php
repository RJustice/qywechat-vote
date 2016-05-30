<?php

namespace App\Http\Controllers\Manage;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\QyVote;
use App\QyVoteNode;
use App\QyVoteRole;
use App\QyVoteUser;
use App\QyVoteRecord;
use DB;
use App\QyUser;
use App\QyGroup;

class VoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(){
        return redirect(url('manage/vote/list'));
    }

    public function create(){

        return view('mvote.create');
    }

    public function store(Request $request){
        $title = $request->input('title');
        $info = $request->input('info');
        $questions = $request->input('q');
        $percents = $request->input('p');
        $type = $request->input('type');
        $forMember = json_decode($request->input('formember'));
        $starttime = strtotime($request->input('starttime'));
        $endtime = strtotime($request->input('endtime'));

        DB::transaction(function() use($title,$info,$questions,$percents,$type,$forMember,$starttime,$endtime){
            $qyvote = QyVote::create([
                    'type' => 1,
                    'title' => $title,
                    'info' => $info,
                    'starttime' => $starttime,
                    'endtime' => $endtime,
                    'status' => 1,
                ]);
            $vid = $qyvote->id;

            foreach($questions as $i=>$question){
                QyVoteNode::create([
                        'title' => $question,
                        'vid' => $vid,
                        'type' => 1,
                        'status' => 1,
                        'percent' => $percents[$i]
                    ]);
            }

            $extra = '';
            foreach($forMember as $userid=>$member){
                QyVoteUser::create([
                        'vid' => $vid,
                        'userid' => $userid,
                        'department' => str_replace(',300,',',',$member->department).',',
                        'name' => $member->name,
                        'position' => $member->position,
                    ]);
                $extra .= ','.$userid.str_replace(',300,', ',', $member->department).',';
            }

            $qyvote->extra = $extra;
            $qyvote->save();
        });

        return redirect('manage/vote/list');

    }

    public function statistics($id,$order){
        $vote = QyVote::find($id);
        if( !$vote ){
            abort(404);
        }
        $order = $order?$order:'asc';
        // $sum = $vote->getRecordsSum()->orderBy('score',$order)->get();
        $sum = $vote->getRecordsSum()
            ->select(DB::raw('*,count(*) as num, sum(score) as total, avg(score) as ss'))
            ->groupBy('vuid')
            ->orderBy('ss',$order)
            ->paginate(15);
        $vuserTotal = $vote->getVoteUser()->count();
        $vduserTotal = $vote->getRecords()->distinct('vuid')->count('vuid');
        $qvuserTotal = $vote->getRecords()->distinct('userid')->count('userid');
        $dp = array_unique(explode(',',str_replace(',,', ',', $vote->extra)));
        // var_dump(array_unique($dp));
        $quserTotal = QyUser::where(function($query) use($dp){
            foreach($dp as $d){
                if( !empty($d) ){
                    $query->orWhere('department','like','%,'.$d.',%');   
                }
            }
        })->distinct()->count('userid');

        return view('mvote.statistics',['vote'=>$vote,'order'=>$order,'sum'=>$sum,'r'=>'statistics','vuserTotal'=>$vuserTotal,'vduserTotal'=>$vduserTotal,'qvuserTotal'=>$qvuserTotal,'quserTotal'=>$quserTotal]);
    }

    public function departStatistics($id){
        $vote = QyVote::find($id);
        if( !$vote ){
            abort(404);
        }

        $groups = QyGroup::all();
        $departments = [];
        // foreach( $groups as $group ){
        //     $departments[$group['group_id']]['name'] = $group['name'];
        //     $departments[$group['group_id']]['total'] = QyUser::where('department','like',"%,{$group['group_id']},%")->distinct('userid')->count('userid');
        //     $departments[$group['group_id']]['vote_sum'] = DB::table('qy_vote_records')->leftJoin('qy_users','qy_vote_records.userid','=','qy_users.userid')->where('qy_users.department','like',"%,{$group['group_id']},%")->where('qy_vote_records.vid','=',$id)->distinct('qy_vote_records.userid')->count('qy_vote_records.userid');
        //     $departments[$group['group_id']]['pid'] = $group['pid'];
        // }
        // $total = [];
        // $vote_sum = [];
        // foreach($departments as $gid=>$department){
        //     if( !isset($total[$department['pid']]) ){
        //         $total[$department['pid']] = 0;
        //         $vote_sum[$department['pid']] = 0;
        //     }
        //     $total[$department['pid']] += $department['total'];
        //     $vote_sum[$department['pid']] += $department['vote_sum'];
        // }

        // return view('mvote.departstatistics',compact('departments','total','vote_sum'));

        $users = QyUser::all();
        $g = [];
        foreach($users as $user){
            $dp = explode(',', str_replace(',,', ',', $user->department));
            foreach($dp as $d){
                if( $d != '' ){
                    $g[$d][] = $user->userid;
                }
            }
        }
        foreach($g as $d=>$gg){
            $gg = array_unique($gg);
            $g[$d] = $gg;
        }

        $pg = [];
        foreach($groups as $group){
            $departments[$group->group_id]['users_sum'] = isset($g[$group->group_id]) ? count($g[$group->group_id]) : 0;
            $departments[$group->group_id]['name'] = $group->name;
            $departments[$group->group_id]['pid'] = $group->pid;
            $pg[$group->pid][] = $group;
            !isset($g[$group->group_id]) ? ( $g[$group->group_id] = [] ) : $g[$group->group_id];
            $departments[$group->group_id]['vu'] = [];
            $departments[$group->group_id]['vdu'] = [];

            $departments[$group->group_id]['vote_sum'] = 0;
            $departments[$group->group_id]['voted_sum'] = 0;
            $departments[$group->group_id]['total'] = isset($g[$group->group_id]) ? count($g[$group->group_id]) : 0;
        }

        $vote_users = $vote->getRecordsSum()->select('userid')->distinct('userid')->get();
        // $voted_users = $vote->getRecordsSum()->select('vid')->distinct('vid')->get();
        $voted_users = $vote->getVoteUser()->get();

        foreach($vote_users as $vote_user){
            foreach($g as $d=>$gg){
                if( in_array($vote_user->userid, $gg) ){
                    // $departments[$d]['vote_sum'] = isset($departments[$d]['vote_sum']) ? $departments[$d]['vote_sum'] : 0;
                    $departments[$d]['vote_sum'] += 1;
                    $departments[$d]['vu'][] = $vote_user->userid;
                }
            }
        }

        foreach($voted_users as $voted_user){
            foreach($g as $d=>$gg){
                if( in_array($voted_user->userid, $gg) ){
                    // $departments[$d]['voted_sum'] = isset($departments[$d]['voted_sum']) ? $departments[$d]['voted_sum'] : 0;
                    $departments[$d]['voted_sum'] += 1;
                    $departments[$d]['vdu'][] = $voted_user->userid;
                }
            }
        }

        foreach($groups as $ggg){
            if( isset($pg[$ggg->group_id]) ){
                $tmp = [];
                $tmp1 = [];
                $tmp2 = [];
                foreach($pg[$ggg->group_id] as $p){
                    $tmp = array_merge($tmp,$g[$p->group_id]);
                    $tmp1 = array_merge($tmp1,$departments[$p->group_id]['vu']);
                    $tmp2 = array_merge($tmp2,$departments[$p->group_id]['vdu']);
                }
                $departments[$ggg->group_id]['voted_sum'] = count(array_unique($tmp2));;
                $departments[$ggg->group_id]['vote_sum'] = count(array_unique($tmp1));;
                $departments[$ggg->group_id]['total'] = count(array_unique($tmp));                
            }
        }
        $departments[1]['total'] = QyUser::all()->count();
        $departments[1]['voted_sum'] = $vote->getRecords()->distinct('vuid')->count('vuid');
        $departments[1]['vote_sum'] = $vote->getRecords()->distinct('userid')->count('userid');

        // foreach($groups as $ggg){
        //     if( $ggg->pid == 1 ){
        //         // echo $ggg->group_id;
        //         // echo "<br />";
        //         // echo $departments[$ggg->group_id]['total'].'+';
        //         // echo "<br />";
        //         $departments[1]['total'] += $departments[$ggg->group_id]['total'];
        //         $departments[1]['voted_sum'] += $departments[$ggg->group_id]['voted_sum'];
        //         $departments[1]['vote_sum'] += $departments[$ggg->group_id]['vote_sum'];
        //     }
        // }
        // echo "<pre>";
        // var_dump($departments);
        // echo "</pre>";

        return view('mvote.departstatistics',compact('departments','vote'));
    }

    public function records($id,$extra = false){
        $vote = QyVote::find($id);
        if( !$vote ){
            abort(404);
        }

        if( $extra ){
            $records = $vote->getRecordsSum()->where('extra','<>','')->orderBy('vuid')->paginate(15);
        }else{            
            $records = $vote->getRecordsSum()->paginate(15);
        }
        return view('mvote.voterecords',['records'=>$records,'vote'=>$vote,'r'=>'records','order'=>'']);
    }

    public function vlist(){
        // $s = ",wanghongyuan,234,300,,dingxiaofeng,234,300,,wuhongqing,234,300,,yaohuang,271,300,,huangxiaokang,237,300,,zhangping1,262,300,,lijun,254,300,,guanjun,270,300,,louyihua,273,300,,liling,246,300,,xiaozhou,246,300,,jinhui,253,300,,jinxiaoli,253,300,,donglijun,279,300,,zhangkun,279,300,,meiyacun,256,300,,zhaowenwei,240,300,,jiawenjun,241,300,,jianghao,265,300,,lihua,255,300,,chenjun,247,300,,panjianwei,247,300,,kuangjue,276,300,,malinyun,236,240,241,247,255,256,265,275,276,300,,daixiaohao,242,300,,sunhui,242,300,,jinyue,250,300,,wangxiaoyan,250,300,,jiangcenru,250,300,,chenjing,272,300,,nilina,272,300,,yuxiaofan,272,300,,xuxiaoling,266,300,,jiangzhuo,266,300,,xukejie,266,300,,meiyujing,261,300,,qinsheng,261,300,,lihui,264,300,,zhanghui,264,300,,zhangqing,264,300,,guwenya,252,300,,zhuzhuoxiao,252,300,,liangjia,263,300,,puxiqian,263,300,,liyongli,251,300,,yuanzheng,251,300,,jiangxiaofeng,257,300,,zhoumin,257,300,,weijia,268,300,,liping,268,300,,songhaibo,268,300,,13951217590,267,300,,shenhuwei,258,300,,zhuguangtong,258,300,,wangjun1,259,300,,zhouli,269,,yaolili,278,,qianbing,243,246,253,254,260,262,270,273,279,300,301,,sunzhigang,239,300,,tongjianfang,271,,qianzhong,250,252,261,263,264,266,272,290,294,300,301,,dongxia,249,252,300,";
        // echo str_replace(',300,', ',', $s);
        // $votes = QyVote::where('starttime','<',time())->where('endtime','>',time())->where('status',1)->get();
        $votes = QyVote::where('status',1)->where('is_deleted',0)->get();
        return view('mvote.vlist',['votes'=>$votes]);
    }

    public function show($id){
        $vote = QyVote::find($id);
        if( !$vote ){
            abort(404);
        }
        return view('mvote.show',['vote'=>$vote]);
    }

    public function youxiu($id,$order,Request $request){
        $vote = QyVote::find($id);
        if( !$vote ){
            abort(404);
        }
        $order = $order?$order:'asc';
        // $sum = $vote->getRecordsSum()->orderBy('score',$order)->get();
        $sum = $vote->getRecordsSum()
            ->select(DB::raw('*,count(*) as num, sum(score) as total, avg(score) as ss'))
            // ->where('ss','>=','90')
            ->groupBy('vuid')
            ->havingRaw('avg(score) >= 90')
            ->orderBy('ss',$order)
            // ->get();
            ->paginate(15);
        
        return view('mvote.statistics',['vote'=>$vote,'order'=>$order,'sum'=>$sum,'r'=>'youxiu']);
    }

    public function lianghao($id,$order){
        $vote = QyVote::find($id);
        if( !$vote ){
            abort(404);
        }
        $order = $order?$order:'asc';
        // $sum = $vote->getRecordsSum()->orderBy('score',$order)->get();
        $sum = $vote->getRecordsSum()
            ->select(DB::raw('*,count(*) as num, sum(score) as total, avg(score) as ss'))
            // ->whereBetween('ss',[71,89])
            ->groupBy('vuid')
            ->havingRaw('avg(score) >= 71 and avg(score) <= 89')
            ->orderBy('ss',$order)
            ->paginate(15);
        
        return view('mvote.statistics',['vote'=>$vote,'order'=>$order,'sum'=>$sum,'r'=>'lianghao']);
    }

    public function hege($id,$order){
        $vote = QyVote::find($id);
        if( !$vote ){
            abort(404);
        }
        $order = $order?$order:'asc';
        // $sum = $vote->getRecordsSum()->orderBy('score',$order)->get();
        $sum = $vote->getRecordsSum()
            ->select(DB::raw('*,count(*) as num, sum(score) as total, avg(score) as ss'))
            // ->whereBetween('ss',[60,70])
            ->groupBy('vuid')
            ->havingRaw('avg(score) >= 60 and avg(score) <= 70')
            ->orderBy('ss',$order)
            ->paginate(15);
        
        return view('mvote.statistics',['vote'=>$vote,'order'=>$order,'sum'=>$sum,'r'=>'hege']);
    }

    public function buhege($id,$order){
        $vote = QyVote::find($id);
        if( !$vote ){
            abort(404);
        }
        $order = $order?$order:'asc';
        // $sum = $vote->getRecordsSum()->orderBy('score',$order)->get();
        $sum = $vote->getRecordsSum()
            ->select(DB::raw('*,count(*) as num, sum(score) as total, avg(score) as ss'))
            // ->where('ss','<','60')
            ->groupBy('vuid')
            ->havingRaw('avg(score) < 60')
            ->orderBy('ss',$order)
            ->paginate(15);
        
        return view('mvote.statistics',['vote'=>$vote,'order'=>$order,'sum'=>$sum,'r'=>'buhege']);
    }

    public function more($id,$vuid,$uid){
        
        $rs = QyVoteRecord::where('vid',$id)
            ->where('vuid',$vuid)
            ->where('userid',$uid)
            ->where('type',0)
            ->get();

        return view('mvote.more',['rs'=>$rs]);

    }

    public function edit($id){
        $vote = QyVote::find($id);
        $selectedMembers = [];
        foreach( $vote->getVoteUser as $selectedMember ){
            $selectedMembers[$selectedMember->userid] = [
                'department' => $selectedMember->department,
                'name' => $selectedMember->name,
                'position' => $selectedMember->position,
            ];
        }
        return view('mvote.edit',['vote'=>$vote,'selectedMembers'=>$selectedMembers]);
    }

    public function update(Request $request, $id){
        $vote = QyVote::find($id);
        if( ! $vote ){
            abort(404);
        }

        $title = $request->input('title');
        $info = $request->input('info');
        $questions = $request->input('q');
        $percents = $request->input('p');
        $type = $request->input('type');
        $forMember = json_decode($request->input('formember'));
        $starttime = strtotime($request->input('starttime'));
        $endtime = strtotime($request->input('endtime'));

        DB::transaction(function() use($id,$title,$info,$questions,$percents,$type,$forMember,$starttime,$endtime){
            // $qyvote = QyVote::create([
            //         'type' => 1,
            //         'title' => $title,
            //         'info' => $info,
            //         'starttime' => $starttime,
            //         'endtime' => $endtime,
            //         'status' => 1,
            //     ]);
            // $vid = $qyvote->id;

            $qyvote = QyVote::find($id);
            $qyvote->title = $title;
            $qyvote->info = $info;
            $qyvote->starttime = $starttime;
            $qyvote->endtime = $endtime;
            $qyvote->status = 1;        

            // foreach($questions as $i=>$question){
            //     QyVoteNode::create([
            //             'title' => $question,
            //             'vid' => $vid,
            //             'type' => 1,
            //             'status' => 1,
            //             'percent' => $percents[$i]
            //         ]);
            // }
             
            foreach($qyvote->getNode as $i=>$node){
                $node->title = $questions[$i];
                $node->percent = $percents[$i];
                $node->save();
            }

            $qyvote->getVoteUser()->delete();
            $extra = '';
            foreach($forMember as $userid=>$member){
                QyVoteUser::create([
                        'vid' => $id,
                        'userid' => $userid,
                        'department' => str_replace(',300,',',',$member->department).',',
                        'name' => $member->name,
                        'position' => $member->position,
                    ]);
                $extra .= ','.$userid.str_replace(',300,',',',$member->department).',';
            }

            $qyvote->extra = $extra;
            $qyvote->save();
        });

        return redirect('manage/vote/list');
    }

    public function del($id){
        $vote = QyVote::find($id);
        if( ! $vote ){
            abort(404);
        }
        $vote->is_deleted = 1;
        $vote->save();
        return redirect('manage/vote/list');
    }

    public function copyx($id){
        $vote = QyVote::find($id);
        if( ! $vote ){
            abort(404);
        }
        $selectedMembers = [];
        foreach( $vote->getVoteUser as $selectedMember ){
            $selectedMembers[$selectedMember->userid] = [
                'department' => $selectedMember->department,
                'name' => $selectedMember->name,
                'position' => $selectedMember->position,
            ];
        }
        return view('mvote.copy',['vote'=>$vote,'selectedMembers'=>$selectedMembers]);
    }

    public function copyxPOST(Request $request, $id){

    }

}

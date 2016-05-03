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
                        'department' => str_replace(',300,',',',$member->department),
                        'name' => $member->name,
                        'position' => $member->position,
                    ]);
                $extra .= ','.$userid.str_replace(',300,', ',', $member->department);
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
            ->get();
        
        return view('mvote.statistics',['vote'=>$vote,'order'=>$order,'sum'=>$sum,'r'=>'statistics']);
    }

    public function records($id){
        $vote = QyVote::find($id);
        if( !$vote ){
            abort(404);
        }

        $records = $vote->getRecordsSum()->get();
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

    public function youxiu($id,$order){
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
            ->havingRaw('ss >= 90')
            ->orderBy('ss',$order)
            ->get();
        
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
            ->havingRaw('ss >= 71 and ss <= 89')
            ->orderBy('ss',$order)
            ->get();
        
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
            ->havingRaw('ss >= 60 and ss <= 70')
            ->orderBy('ss',$order)
            ->get();
        
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
            ->havingRaw('ss < 60')
            ->orderBy('ss',$order)
            ->get();
        
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

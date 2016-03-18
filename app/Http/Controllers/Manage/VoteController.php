<?php

namespace App\Http\Controllers\Manage;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\QyVote;
use App\QyVoteNode;
use App\QyVoteRole;
use App\QyVoteUser;
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
                        'department' => $member->department,
                        'name' => $member->name,
                        'position' => $member->position,
                    ]);
                $extra .= ','.$userid.$member->department;
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
        
        return view('mvote.statistics',['vote'=>$vote,'order'=>$order,'sum'=>$sum]);
    }

    public function vlist(){
        $votes = QyVote::where('starttime','<',time())->where('endtime','>',time())->where('status',1)->get();
        return view('mvote.vlist',['votes'=>$votes]);
    }

    public function show($id){
        $vote = QyVote::find($id);
        if( !$vote ){
            abort(404);
        }
        return view('mvote.show',['vote'=>$vote]);
    }
}

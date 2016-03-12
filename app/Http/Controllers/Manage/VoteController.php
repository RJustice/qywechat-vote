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
    public function index(){
        return 'votes';
    }

    public function create(){
        /*QyVote::insert([
                'type' => 1,
                'title' => '四月评测',
                'info' => '四月份评测,希望大家认真选择',
                'starttime' => mktime(0,0,0,3,9,2016),
                'endtime' => mktime(0,0,0,3,11,2016),
                'ym' => date('ym'),
                'status' => 1,
            ]);
        return QyVote::all()->last();*/
        // var_dump($request);return 1;
        // $title = $request->input('title');
        // $info = $request->input('info');
        // $question = $request->input('q');
        // $percent = $request->input('p');
        // $type = $request->input('type');
        // $forMember = $request->input('formember');

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
            // $qyvote = new QyVote();
            // $qyvote->type = 1;
            // $qyvote->title = $title;
            // $qyvote->info = $info;
            // $qyvote->starttime = $starttime;
            // $qyvote->endtime = $endtime;
            // $qyvote->status = 1;
            // $qyvote->save();
            // $vid = $qyvote->id;

            $qyvote = QyVote::create([
                    'type' => 1,
                    'title' => $title,
                    'info' => $info,
                    'starttime' => $starttime,
                    'endtime' => $endtime,
                    'status' => 1
                ]);
            $vid = $qyvote->id;
            // $qyvotenode = new QyVoteNode();
            foreach($questions as $i=>$question){
                QyVoteNode::create([
                        'title' => $question,
                        'vid' => $vid,
                        'type' => 1,
                        'status' => 1,
                        'percent' => $percents[$i]
                    ]);
            }

            foreach($forMember as $userid=>$member){
                QyVoteUser::create([
                        'vid' => $vid,
                        'userid' => $userid,
                        'department' => $member->department,
                        'name' => $member->name,
                        'position' => $member->position,
                    ]);
            }
        });

        return 1;

    }

    public function statistics($id){

    }
}

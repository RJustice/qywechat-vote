<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Redirect,Input,DB;
use App\QyVote as Vote;
use \App\Classes\QyWechat;
use App\QyVoteUser;
use App\QyUser;
use App\QyVoteRecord;
use App\QyVoteNode;
use App\QyVoteRole;
// use Session;

class QyVoteController extends Controller
{
    private $appid = "wxaeff15efe968850c";
    private $secret = "9NEi0eWG8im-L_amE_bdw5CsWrSf9Pw3Af-z77fHg7sv_mcxEN7kOxIC0XCAe8Uq";
    private $agentid = 46;
    private $scope = "snsapi_base";
    private $response_type = "code";
    // private $redirect_uri = route('vote');
    private $debug = false;

    protected $qyWechat = '';

    protected $_vuser = '';
    protected $_user = '';
    protected $_vid = '';
    protected $_vote = '';

    const ISINARRAY = 0;
    const ISUPLEVEL = 1;
    const ISVOTED = 2;

    public function __construct(){
        $options = [
            'appid' => $this->appid,
            'appsecret' => $this->secret,
            'agentid' => $this->agentid,
            'debug' => true,
            // 'redirect_uri' => $this->redirect_uri,
        ];
        $this->qyWechat = new QyWechat($options);
    }

    public function voteList(Request $request){
        $code = $request->input('code');
        if( $userid = $this->_checkQyUser($code) ){
            $user = QyUser::where('userid',$userid)->first();
            $dps = explode(',', trim($user->department,','));
            $role = QyVoteRole::where('userid',$userid)->first();
            $votes = Vote::where('starttime','<',time())
                ->where('endtime','>',time())
                ->where('status',1)
                ->where(function($query) use($user,$dps){
                    $query->where('extra','not like','%,'.$user->userid.',%');
                    $query->where(function($query) use($dps){
                        foreach($dps as $dp){
                            $query->orWhere('extra','like','%,'.$dp.',%');
                        }
                    });
                })
                ->get();
            return view('vote.list',['votes'=>$votes,'userid'=>$userid,'role'=>$role]);
        }else{
            return view('vote.need_qy_member');
        }
    }

    public function vote($id,Request $request){
        $code = $request->input('code');
        $vid = $id;
        if( $userid = $this->_checkQyUser($code) ){
            // $userid = 'yuntao';
            $user = $this->qyWechat->getUserInfo($userid);
            $vusers = QyVoteUser::where('vid',$vid)
                        ->where(function($query) use($user){
                                foreach($user['department'] as $department){
                                    $query->orWhere('department','LIKE',"%,".$department.",%");
                                }
                            })
                        ->where('userid','<>',$userid)
                        ->get()
                        ->toArray();
            
            $vote = Vote::find($vid);
            if( !$vote ){
                return view('vote.no_vote');
            }

            if( empty($vusers) ){
                return view('vote.no_vote_user');
            }
            return view('vote.view',['vusers'=>$vusers,'vote'=>$vote]);
        }else{
            return view('vote.need_qy_member');
        }
    }

    public function postVote(Request $request){
        $vid = $request->input('vid');
        $vuserid = $request->input('vuserid');
        $qscore = $request->input('vscore');
        $extra = $request->input('extra');

        $userid = session('userid');
        // $userid = 'yuntao';
        $this->_vote = Vote::where('id',$vid)->where('starttime','<',time())->where('endtime','>',time())->first();
        $this->_vuser = QyVoteUser::where('userid',$vuserid)->where('vid',$vid)->first();
        $this->_user = QyUser::where('userid',$userid)->first();

        if( !$this->_vote || !$this->_vuser || !$this->_user ){
            return view('vote.error');
        }

        $flag = $this->_checkAccess();
        if( $flag !== TRUE ){
            return view('vote.noaccess',['flag'=>$flag]);
        }

        $total = 0;
        foreach($qscore as $vnodeid=>$score){
            $vnode = QyVoteNode::where('id',$vnodeid)->first();
            QyVoteRecord::create([
                    'vid' => $this->_vote->id,
                    'score' => $score * 2,
                    'userid' => $this->_user->userid,
                    'vuid' => $this->_vuser->userid,
                    'name' => $this->_vuser->name,
                    'vnodeid' => $vnodeid,
                    'ym' => date('Ym'),
                    'type' => 0
                ]);
            $total += $score * 2;
        }

        QyVoteRecord::create([
                'vid' => $this->_vote->id,
                'score' => $total,
                'userid' => $this->_user->userid,
                'vuid' => $this->_vuser->userid,
                'name' => $this->_vuser->name,
                'vnodeid' => 0,
                'ym' => date('Ym'),
                'type' => 1,
                'extra' => $extra
            ]);
        return redirect(url('vlist'));
    }
    
    public function voteApp(){
        $redirect_uri = route('vlist');
        return redirect($this->qyWechat->getOauthRedirect($redirect_uri));
    }

    protected function _checkQyUser($code){
        if(session('userid')){
            return session('userid');
        }else{
            $user = $this->qyWechat->getUserId($code);
            if( isset($user['UserId']) ){
                session(['userid'=>$user['UserId']]);
                return $user['UserId'];
            }else{
                return false;
            }
        }
    }

    protected function _checkAccess(){
        // 是否同样在评测对象中
        $vusers = array_pluck($this->_vote->getVoteUser()->get()->toArray(),'userid');
        if( in_array($this->_user->userid,$vusers) ){
            return static::ISINARRAY;
        }

        // 是否是部门上级
        $vdepart = array_flip(explode(',',trim($this->_vuser->department,',')));
        $udepart = array_flip(explode(',',trim($this->_user->department,',')));

        if(!array_intersect_key($vdepart,$udepart)){
            return static::ISUPLEVEL;
        }

        // 已经投过票
        $voted = QyVoteRecord::where('vid',$this->_vote->id)->where('userid',$this->_user->userid)->where('vuid',$this->_vuser->userid)->first();
        if( $voted ){
            return static::ISVOTED;
        }

        return true;
    }

    public function wechatStatistics($id = null,$order = 'asc'){
        if( session()->has('userid') ){
            $userid = session('userid');
            $role = QyVoteRole::where('userid',$userid)->first();
            if( !$role ){
                abort(404);
            }

            if( $id === null ){
                $votes = Vote::where('status',1)->get();
                return view('vote.statistics_list',['votes'=>$votes]);
            }

            $vote = Vote::where('id',$id)->first();

            if( ! $vote ){
                abort(404);
            }
            // $order = $request->input('order')?$request->input('order'):'asc';
            $sum = $vote->getRecordsSum()
            ->select(DB::raw('*,count(*) as num, sum(score) as total, avg(score) as ss'))
            ->groupBy('vuid')
            ->orderBy('ss1',$order)
            ->get();
            var_dump($sum);
            return view('vote.statistics',['vote'=>$vote,'sum'=>$sum,'order'=>$order]); 
        }       
    }

    protected function _checkStatisticsAccess(){

    }
}

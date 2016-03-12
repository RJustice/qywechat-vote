<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Redirect,Input;
use App\QyVote as Vote;
use \App\Classes\QyWechat;
use App\QyVoteUser;
use App\QyUser;
use App\QyVoteRecord;
use App\QyVoteNode;
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

    public function vote(Request $request){
        $code = $request->input('code');
        $vid = 14;
        if( $userid = $this->_checkQyUser($code) ){
            // $userid = 'yuntao';
            $user = $this->qyWechat->getUserInfo($userid);
            $vusers = QyVoteUser::where('vid',$vid)
                        ->Where(function($query) use($user){
                                foreach($user['department'] as $department){
                                    $query->orWhere('department','LIKE',"%,".$department.",%");
                                }
                            })->get()->toArray();
            
            $vote = Vote::find($vid);
            if( !$vote ){
                return 'no such vote';
            }

            if( empty($vusers) ){
                return view('vote.no_vote');
            }
            return view('vote.view',['vusers'=>$vusers,'vote'=>$vote]);
        }else{
            return "企业内部员工才能评测";
        }
    }

    public function postVote(Request $request){
        $vid = $request->input('vid');
        $vuserid = $request->input('vuserid');
        $qscore = $request->input('vscore');

        $userid = session('userid');
        // $userid = 'yuntao';
        // var_dump($userid);return;
        // var_dump($vid,$vuserid); return;
        $this->_vote = Vote::where('id',$vid)->where('starttime','<',time())->where('endtime','>',time())->first();
        $this->_vuser = QyVoteUser::where('userid',$vuserid)->where('vid',$vid)->first();
        $this->_user = QyUser::where('userid',$userid)->first();

        if( !$this->_vote || !$this->_vuser || !$this->_user ){
            return 'no';
        }

        if( ! $this->_checkAccess() ){
            return 'no access';
        }

        foreach($qscore as $vnodeid=>$score){
            $vnode = QyVoteNode::where('id',$vnodeid)->first();
            QyVoteRecord::create([
                    'vid' => $this->_vote->id,
                    'score' => $score * $vnode->percent / 100,
                    'userid' => $this->_user->userid,
                    'vuid' => $this->_vuser->userid,
                    'name' => $this->_vuser->name,
                    'vnodeid' => $vnodeid,
                    'ym' => date('Ym')
                ]);
        }

        return 'done';
    }
    
    public function voteApp(){
        $redirect_uri = route('vote');
        return redirect($this->qyWechat->getOauthRedirect($redirect_uri));
    }

    protected function _checkQyUser($code){
        if(session('userid')){
            // var_dump(1);
            // var_dump(session('userid'));
            // exit;
            return session('userid');
        }else{
            $user = $this->qyWechat->getUserId($code);
            if( isset($user['UserId']) ){
                session(['userid'=>$user['UserId']]);
                // var_dump(2);
                // var_dump(session('userid'));
                // exit;
                return $user['UserId'];
            }else{
                // var_dump(3);
                // var_dump(Session::all());
                // exit;
                return false;
            }
        }
    }

    protected function _checkAccess(){
        // 是否同样在评测对象中
        $vusers = array_pluck($this->_vote->getVoteUser()->get()->toArray(),'userid');
        if( in_array($this->_user->userid,$vusers) ){
            return false;
        }

        // 是否是部门上级
        $vdepart = array_flip(explode(',',trim($this->_vuser->department,',')));
        $udepart = array_flip(explode(',',trim($this->_user->department,',')));

        if(!array_intersect_key($vdepart,$udepart)){
            return false;
        }

        // 已经投过票
        $voted = QyVoteRecord::where('vid',$this->_vote->id)->where('userid',$this->_user->userid)->where('vuid',$this->_vuser->userid)->first();
        if( $voted ){
            return false;
        }

        return true;
    }

    public function WechatStatistics($id){
        $vote = QyVote::where('id',$id)->first();
        if( ! $vote ){
            abort(404);
        }        
       
    }

    protected function _checkStatisticsAccess(){

    }
}

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h3 class="text-center">{{ $vote->title }}</h3>
            <div class="btn-group pull-right">
                <a href="{{ url('manage/vote/list') }}" class="btn btn-default">返回评测列表</a>
            </div>
            <div class="btn-group pull-right">
                <a href="{{ url('manage/vote/'.$r,['id'=>$vote->id,'order'=>'asc']) }}" class="btn @if($order == '' || $order == 'asc') btn-success @else btn-default @endif">由低到高</a>
                <a href="{{ url('manage/vote/'.$r,['id'=>$vote->id,'order'=>'desc']) }}" class="btn @if($order == 'desc') btn-success @else btn-default @endif">由高到低</a>
            </div>
            <div class="btn-group pull-left" style="margin-right:30px;">
                <a href="{{ url('manage/vote/records',['id'=>$vote->id]) }}" class="btn btn-default">投票人统计</a>
            </div>
            <div class="btn-group pull-left">            
                <a href="{{ url('manage/vote/statistics',['id'=>$vote->id,'order'=>'desc']) }}" class="btn btn-default @if($r == 'statistics') btn-success @endif">全部排名</a>
                <a href="{{ url('manage/vote/youxiu',['id'=>$vote->id,'order'=>'desc']) }}" class="btn btn-default @if($r == 'youxiu') btn-success @endif">优秀排名</a>
                <a href="{{ url('manage/vote/lianghao',['id'=>$vote->id,'order'=>'desc']) }}" class="btn btn-default @if($r == 'lianghao') btn-success @endif">良好排名</a>
                <a href="{{ url('manage/vote/hege',['id'=>$vote->id,'order'=>'desc']) }}" class="btn btn-default @if($r == 'hege') btn-success @endif">合格排名</a>
                <a href="{{ url('manage/vote/buhege',['id'=>$vote->id,'order'=>'desc']) }}" class="btn btn-default @if($r == 'buhege') btn-success @endif">不合格排名</a>
            </div>
        </div>
        <br /><br />
        <div class="col-md-12">
            <ul class="list-group">
                <li class="list-group-item">
                    <div class="row text-center">
                        <div class="col-md-3">部门</div>
                        <div class="col-md-1">被投人</div>
                        <div class="col-md-1">投票人</div>
                        <div class="col-md-1">分数</div>
                        <div class="col-md-6">补充</div>
                    </div>
                </li>
                @foreach($records as $s)
                <li class="list-group-item">
                    <div class="row">
                        <div class="col-md-3">@foreach($s->getVUser->getDepartment()->get() as $d ) {{ $d->name }} | @endforeach</div>
                        <div class="col-md-1 text-center">{{ $s->name }}</div>
                        <div class="col-md-1 text-center">{{ $s->getWhoVote->name }}</div>
                        <div class="col-md-1 text-center">{{ number_format($s->score,2) }}</div>
                        <div class="col-md-6 text-center">{{ $s->extra }}</div>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h3 class="text-center">{{ $vote->title }}</h3>
            <div class="btn-group">
                <a href="{{ url('manage/vote/statistics',['id'=>$vote->id,'order'=>'asc']) }}" class="btn btn-default">由低到高</a>
                <a href="{{ url('manage/vote/statistics',['id'=>$vote->id,'order'=>'desc']) }}" class="btn  btn-default">由高到低</a>
                <a href="{{ url('manage/vote/records',['id'=>$vote->id]) }}" class="btn btn-success">投票人统计</a>
            </div>
            <div class="btn-group">
                <a href="{{ url('manage/vote/list') }}" class="btn btn-default">返回评测列表</a>
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
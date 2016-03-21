@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h3 class="text-center">{{ $vote->title }}</h3>
            <div class="btn-group">
                <a href="{{ url('manage/vote/statistics',['id'=>$vote->id,'order'=>'asc']) }}" class="btn @if($order == '' || $order == 'asc') btn-success @else btn-default @endif">由低到高</a>
                <a href="{{ url('manage/vote/statistics',['id'=>$vote->id,'order'=>'desc']) }}" class="btn @if($order == 'desc') btn-success @else btn-default @endif">由高到低</a>
                <a href="{{ url('manage/vote/records',['id'=>$vote->id]) }}" class="btn btn-default">投票人统计</a>
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
                        <div class="col-md-6">部门</div>
                        <div class="col-md-2">姓名</div>
                        <div class="col-md-2">评测分数</div>
                        <div class="col-md-2">评测人数</div>
                    </div>
                </li>
                @foreach($sum as $s)
                <li class="list-group-item">
                    <div class="row">
                        <div class="col-md-6">@foreach($s->getVUser->getDepartment()->get() as $d ) {{ $d->name }} | @endforeach</div>
                        <div class="col-md-2 text-center">{{ $s->name }}</div>
                        <div class="col-md-2 text-center">{{ number_format($s->ss,2) }}</div>
                        <div class="col-md-2 text-center">{{ $s->num }}</div>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection
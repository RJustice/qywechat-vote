@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row" style="margin-bottom:20px">
        <div class="col-md-10 col-md-offset-1">
            <div class="btn-group">
                <a href="{{ url('manage/vote/records',['id'=>$rs->first()->vote->id]) }}" class="btn btn-info">返回</a>
            </div>
        </div>
    </div>
    <div class="row" style="margin-bottom:20px;">
        <div class="col-md-10 col-md-offset-1">
            <h4 class="text-center">{{ $rs->first()->vote->title }}</h4>
        </div>
    </div>
    <div class="row" style="margin-bottom:20px;">
        <div class="col-md-10 col-md-offset-1">
            <div class="row">
                <div class="col-md-2"><p>被评测人:</p></div>
                <div class="col-md-3"><p style="font-size:15px;line-height: 18px;height：18px;font-weight: 600;">{{ $rs->first()->name }}</p></div>
            </div>
            <div class="row">
                <div class="col-md-2"><p>评测人:</p></div>
                <div class="col-md-3"><p style="font-size:15px;line-height: 18px;height：18px;font-weight: 600;">{{ $rs->first()->getWhoVote->name }}</p></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <ul class="list-group col-md-5">
                @foreach($rs as $r)
                <li class="list-group-item"><p>{{ $r->node->title }} <span class="pull-right" style="font-weight: 600;color:#f4645f;margin-right:40px;">{{ $r->score }}</p></li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection

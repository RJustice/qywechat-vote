@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="text-center">{{ $vote->title }}</h4>
    <div class="row">
        <div class="col-md-10 col-md-offset-1 text-center">
            <div class="btn-group">
                <a href="{{ url('statistics',['id'=>$vote->id,'order'=>'asc']) }}" class="btn btn-default @if($order == 'asc') btn-success @endif">由低到高</a>
                <a href="{{ url('statistics',['id'=>$vote->id,'order'=>'desc']) }}" class="btn btn-default @if($order == 'desc') btn-success @endif">由高到低</a>
            </div>
        </div>
    </div>
    <br /><br />
    <div class="col-md-10 col-md-offset-1">
        <ul class="list-group">
            <li class="list-group-item">
                <div class="row text-center">
                    <div class="col-xs-6">姓名</div>
                    <div class="col-xs-6">分数</div>
                </div>
            </li>
            @foreach($sum as $s)
            <li class="list-group-item">
                <div class="row">
                    <div class="col-xs-6 text-center">{{ $s->name }}</div>
                    <div class="col-xs-6 text-center">{{ number_format($s->ss,2) }}</div>
                </div>
            </li>
            @endforeach
        </ul>
    </div>
</div>
@endsection
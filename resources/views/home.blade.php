@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="btn-group">
                <a href="{{ url('manage/sync') }}" class="btn btn-default" id="sync">同步通讯录</a>
                <a href="{{ url('manage/vote/create') }}" class="btn btn-default">创建评测</a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <h3 class="text-center">历次月度评测</h3>
            <ul class="list-group">
                @foreach($votes as $vote)
                <li class="list-group-item">
                    <p>
                        <a href="{{ url('manage/vote/show',$vote->id) }}">{{ $vote->title }}</a>
                        <a href="{{ url('manage/vote/statistics',['id'=>$vote->id,'order'=>'asc']) }}" class="btn btn-primary pull-right">查看统计</a>
                    </p>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection

@section('js')
@endsection
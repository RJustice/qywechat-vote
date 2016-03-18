@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            @if( !$votes->isEmpty())
            <div class="list-group">
                @foreach($votes as $vote)
                <p><a href="{{ url('vote',$vote->id) }}" class="list-group-item">{{ $vote->title }}</a></p>
                @endforeach
            </div>
            @else
            <div class="alert alert-info text-center">
                <p>暂时没有需要参与月度评测项目</p>
            </div>
            @endif
        </div>
    </div>
    @if($userid == 'yuntao')
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <a href="{{ url('vote/') }}" class="btn btn-info btn-block">统计页面</a>
        </div>
    </div>
    @endif
</div>
@endsection

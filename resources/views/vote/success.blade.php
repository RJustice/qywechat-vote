@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="alert alert-info">
                <h3 class="text-center">评测成功</h3>
                @if(!$vusers->isEmpty())
                <h4 class="text-center">您还有需要评测的领导,请继续点击评测</h4>
                <div class="list-group">
                @foreach($votes as $vote)
                    <p><a href="{{ url('vote',$vote->id) }}" class="list-group-item">{{ $vote->title }}</a></p>
                @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
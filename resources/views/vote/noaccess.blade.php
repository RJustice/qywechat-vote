@extends('layouts.app')

@section('content')
<div class="container">
    <div class="col-md-10 col-md-offset-1">
        <div class="alert alert-danger text-center">
            @if(\App\Http\Controllers\QyVoteController::ISINARRAY == $flag)
            <h3>不能同级评测</h3>
            @elseif(\App\Http\Controllers\QyVoteController::ISUPLEVEL == $flag)
            <h3>不能评测非本部门的主管</h3>
            @elseif(\App\Http\Controllers\QyVoteController::ISVOTED == $flag)
            <h3>您已经评测过</h3>
            @endif
            <h3></h3>
        </div>
    </div>
</div>
@endsection

@section('js')
@endsection
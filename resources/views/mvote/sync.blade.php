@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="alert alert-info">
                <h3>通讯录同步成功</h3>
                <p><a href="{{ url('manage/vote/list') }}">点击返回</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
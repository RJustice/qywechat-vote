@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <form action="{{ url('manage/users') }}" method="post">
                <div class="form-group">
                    <label for="name">姓名</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="输入姓名">
                </div>
                <div class="form-group">
                    <label for="email">用户名</label>
                    <input type="text" name="email" id="email" class="form-control" placeholder="输入登录用户名">
                </div>
                <div class="form-group">
                    <label for="password">用户名</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="输入密码">
                </div>
                <div class="form-group">
                    {{ csrf_field() }}
                    <button type="submit" class="btn btn-success btn-block">创建</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
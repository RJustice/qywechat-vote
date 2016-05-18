@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <h2>修改管理员: {{ $user->name }} </h2>
            <form action="{{ url('manage/users',[$user->id]) }}" method="post">
                <div class="form-group {{ $errors->has('error') ? 'has-error' : '' }}">
                    <label for="password">原始密码</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="输入原始密码" value="">
                    {{ $errors->first('error') }}
                </div>

                <div class="form-group">
                    <label for="new_password">新密码</label>
                    <input type="password" name="new_password" id="new_password" class="form-control" placeholder="输入新密码" value="">
                </div>

                <div class="form-group">
                    {{ csrf_field() }}
                    {{ method_field('PUT') }}
                    <button type="submit" class="btn btn-success btn-block">编辑</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="btn-group">                
                <a href="{{ url('manage/vote/list') }}" class="btn btn-info">首页</a>
            </div>

            <div class="btn-group pull-right">                
                <a href="{{ url('manage/users/create') }}" class="btn btn-info">新增管理员</a>
            </div>
        </div>
    </div>
    <div class="row m20">
        <div class="col-md-10 col-md-offset-1">

            <div class="row">
                <div class="col-md-3">姓名</div>
                <div class="col-md-3">用户名</div>
                <div class="col-md-2 pull-right">操作</div>
            </div>
            <ul class="list-group">
                <?php foreach( $users as $user ): ?>
                <li class="list-group-item">
                    <div class="row">
                        <div class="col-md-3">{{ $user->name }}</div>
                        <div class="col-md-3">{{ $user->email }}</div>
                        <div class="col-md-2 pull-right">
                            <a href="{{ url("manage/users/{$user->id}/edit") }}" class="btn btn-info">编辑</a>
                            <form action="{{ url('manage/users',[$user->id])}}" method="post" class="pull-right" onsubmit="return delA(this)">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="DELETE">
                                <input type="submit" class="btn btn-danger" value="删除">
                            </form>
                        </div>
                    </div>
                </li>
                <?php endforeach;?>
            </ul>
        </div>
    </div>
</div>
@endsection

@section('js')
<script type="text/javascript">
    function delA(t){
        var r = confirm('删除该管理员?');
        if( r == true ){
            return true;
        }else{
            return false;
        }
    }
</script>
@endsection
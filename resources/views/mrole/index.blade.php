@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="btn-group">
                <a href="{{ url('manage/vote/list') }}" class="btn btn-info">首页</a>
            </div>
            <div class="btn-group">
                <a href="{{ url('manage/role/create') }}" class="btn btn-info">添加</a>
            </div>
        </div>
    </div>
    <div class="row m20">
        <div class="col-md-10 col-md-offset-1">
            <ul class="list-group">
                <?php foreach($users as $user): ?>
                <li class="list-group-item">
                    <div class="row">
                        <div class="col-md-3">
                            {{ $user->vuser->name }}
                        </div>
                        <div class="col-md-3 pull-right">
                            <form action="{{ url('manage/role',[$user->id])}}" method="post" class="pull-right" onsubmit="return delA(this)">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="DELETE">
                                <input type="submit" class="btn btn-danger" value="删除">
                            </form>
                        </div>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
@endsection

@section('js')
<script type="text/javascript">
    function delA(t){
        var r = confirm('删除确认?');
        if( r == true ){
            return true;
        }else{
            return false;
        }
    }
</script>
@endsection

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="btn-group">                
                <a href="{{ url('manage/vote/create') }}" class="btn btn-info">创建评测</a>
            </div>
            <?php if( Auth::user()->id === 1 ): ?>
            <div class="btn-group">                
                <a href="{{ url('manage/users') }}" class="btn btn-info">管理员</a>
            </div>
            <div class="btn-group">
                <a href="{{ url('manage/role') }}" class="btn btn-info">微信查看权限</a>
            </div>
            <?php else: ?>            
            <div class="btn-group">
                <a href="{{ url('manage/users/'.Auth::user()->id.'/edit') }}" class="btn btn-info">修改管理密码</a>
            </div>
            <?php endif;?>

            <div class="btn-group pull-right">
                <a href="{{ url('manage/sync') }}" class="btn btn-default" id="sync">同步通讯录</a>
                <!-- <a href="{{ url('manage/role') }}" class="btn btn-default" id="role">微信统计权限</a> -->
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <h3 class="text-center">历次月度评测</h3>
            <ul class="list-group">
                @foreach($votes as $v)
                <li class="list-group-item">
                    <p>
                        <a href="{{ url('manage/vote/show',$v->id) }}">{{ $v->title }}</a>
                        
                        <span class="btn-group pull-right">
                            <a href="{{ url('manage/vote/statistics',['id'=>$v->id,'order'=>'asc']) }}" class="btn btn-primary">查看统计</a>
                            <a href="{{ url('manage/vote/edit',['id'=>$v->id]) }}" class="btn btn-primary">编辑</a>
                            <a href="javascript:;" class="btn btn-primary" onclick="delV({{ $v->id }})">删除</a>
                            <a href="{{ url('manage/vote/copy',['id'=>$v->id]) }}" class="btn btn-primary">复制</a>
                        </span>
                    </p>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection

@section('js')
<script type="text/javascript">
    function delV(id){
        var r = confirm('确认删除该民意测试?');
        if( r == true ){
            window.location.href = '{{ url('manage/vote/del') }}/'+id;
        }
    }
</script>
@endsection
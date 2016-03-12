@extends('layouts.app')
<style type="text/css">
.selected{background:#5cb85c;}
</style>
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <h2 class="text-center">{{ $vote->title }}</h2>
            <p>{{ $vote->info }}</p>
            <form action="{{ url('vote') }}" method="post" id="voteForm">
                <div class="row">
                    <div class="col-md-12">
                        <p>选择要评测的部门领导</p>
                        @foreach( $vusers as $k=>$vuser )
                        <div class="col-md-3 col-xs-6 vuser @if($k == 0) selected @endif" data-userid="{{ $vuser['userid'] }}" data-department="{{ $vuser['department'] }}">{{ $vuser['name'] }}</div>
                        @endforeach
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h4>以下是评测内容：</h4>
                        @foreach( $vote->getNode()->get() as $vnode)
                        <p><span class="num">{{ $vnode->id }}:</span>{{ $vnode->title }} <span class="percent">所占百分比({{ $vnode->percent }}%)</span></p>
                        <p><input type="text" name="vscore[{{ $vnode->id }}]" id="vscore_{{ $vnode->id }}" class="form-control"></p>
                        @endforeach
                    </div>
                </div>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="vuserid" value="{{ $vusers[0]['userid'] }}" id="vuser">
                <input type="hidden" name="vid" value="{{ $vote->id }}">
                <div class="input-group">
                    <input type="submit" value="提交" class="btn btn-block btn-primary">
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script type="text/javascript">
    $(function(){
        $(".vuser").on('click',function(){
            var userid = $(this).data('userid');
            var department = $(this).data('department');
            if( $(this).hasClass('selected') ){
                return ;
            }else{
                $(this).siblings().removeClass('selected');
                $(this).addClass('selected');
                $("#vuser").val(userid);
            }
        });
    });
</script>
@endsection
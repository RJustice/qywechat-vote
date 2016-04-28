@extends('layouts.app')
<style type="text/css">
.vuser{cursor: pointer;}
.selected{background: #5cb85c;}
</style>
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <h2 class="text-center">{{ $vote->title }}</h2>
            <p>{!! $vote->info !!}</p>
            <form action="{{ url('vote') }}" method="post" id="voteForm">
                <div class="row">
                    <div class="col-md-12">
                        <p>选择要评测的部门领导</p>
                        @foreach( $vusers as $k=>$vuser )
                        <a class="btn @if($k==0) btn-success @else btn-default @endif vuser" data-userid="{{ $vuser['userid'] }}" data-department="{{ $vuser['department'] }}">{{ $vuser['name'] }}</a>
                        @endforeach
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h4>以下是评测内容：</h4>
                        <hr />
                        @foreach( $vote->getNode()->get() as $k=>$vnode)
                        <h4><span class="num">{{ $k+1 }}:</span>{{ $vnode->title }} </h4>
                        <p><input type="text" name="vscore[{{ $vnode->id }}]" id="vscore_{{ $vnode->id }}" class="ratingx" required value="1" data-min="1" data-max="5"></p>
                        @endforeach
                    </div>
                    <div class="col-md-12">
                        <h4>意见建议或投诉举报:</h4>
                        <textarea name="extra" id="extra" cols="30" rows="10" class="form-control"></textarea>
                    </div>
                </div>
                <hr />
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="vuserid" value="{{ $vusers[0]['userid'] }}" id="vuser">
                <input type="hidden" name="vid" value="{{ $vote->id }}">
                <div class="row" style="margin-bottom:20px;">
                    <div class="col-md-10 col-md-offset-1">
                        <input type="submit" value="提交" class="btn btn-block btn-success btn-lg" >
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<link rel="stylesheet" href="{{ asset('style/css/star-rating.css') }}" media="all">
<link rel="stylesheet" href="{{ asset('style/css/theme-krajee-svg.min.css') }}" media="all">
<script type="text/javascript" src="{{ asset('style/js/star-rating.min.js') }}"></script>
<script type="text/javascript">
    $(function(){
        $(".vuser").on('click',function(){
            var userid = $(this).data('userid');
            var department = $(this).data('department');
            if( $(this).hasClass('btn-success') ){
                return ;
            }else{
                $(this).siblings().removeClass('btn-success').addClass('btn-default');
                $(this).addClass('btn-success');
                $("#vuser").val(userid);
            }
        });

        $(".ratingx").rating({
            min : 1,
            max : 5,
            step : 1,
            showClear:false,
            showCaption:false,
            size:"md"
        });
    });
</script>
@endsection
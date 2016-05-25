@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
    <div class="col-md-10 col-md-offset-1" style="margin-bottom:20px;">
        <h3 class="text-center">{{ $vote->title }}</h3>

        <div class="btn-group pull-left">
            <a href="{{ url('manage/vote/list') }}" class="btn btn-default">返回评测列表</a>
        </div>
    </div>
    </div>

    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <ul class="list-group">
                <li class="list-group-item">
                    <div class="row">
                        <div class="col-md-3 text-center">部门</div>
                        <div class="col-md-2 text-center">总人数</div>
                        <div class="col-md-2 text-center">参与人数</div>
                        <div class="col-md-2 text-center">需评测人数</div>
                        <div class="col-md-2 pull-right text-center">比例</div>
                    </div>
                </li>
                @foreach($departments as $gid=>$department)
                <li class="list-group-item">
                    <div class="row">
                        @if($department['pid'] > 1 )
                        <div class="col-md-1">|------</div>
                        <div class="col-md-2 text-left">{{ $department['name'] }}</div>
                        @else
                        <div class="col-md-3 text-left">{{ $department['name'] }}</div>
                        @endif

                        <div class="col-md-2 text-center">{{ $department['total'] }}</div>
                        <div class="col-md-2 text-center">{{ $department['vote_sum'] }}</div>
                        <div class="col-md-2 text-center">{{ $department['voted_sum'] }}</div>
                        
                        @if($department['total'] <> 0 )
                        <div class="col-md-2 pull-right text-center">
                            {{ number_format($department['vote_sum'] / $department['total'] , 2) * 100 }}%
                        </div>
                        @else
                        <div class="col-md-2 pull-right text-center">
                            0
                        </div>
                        @endif
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <ul class="list-group">
                @foreach($votes as $vote)
                <a href="{{ url('statistics',$vote->id) }}" class="list-group-item">
                    {{ $vote->title }}
                </a>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection
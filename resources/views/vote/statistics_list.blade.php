@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <ul class="list-group">
                @foreach($votes as $vote)
                <li class="list-group-item">
                    {{ $vote->title }}
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection
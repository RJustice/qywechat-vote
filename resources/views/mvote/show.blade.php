@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <h3 class="text-center">{{ $vote->title }}</h3>
            <p>{!! $vote->info !!}</p>
            <ul class="list-group">
                @foreach($vote->getNode()->get() as $node)
                <li class="list-group-item">{{ $node->title }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection

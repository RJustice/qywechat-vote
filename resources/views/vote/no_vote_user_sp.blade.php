@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="alert alert-danger text-center">
                <h3>您是被评测人</h3>
                <h3>不能对其他人进行评测</h3>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
@endsection
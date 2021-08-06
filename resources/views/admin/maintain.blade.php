@extends('layouts.head')
@section('content')
@if (\Session::has('success'))
<div class="alert alert-success alert-dismissible">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    {!! \Session::get('success') !!}
</div>
@elseif (\Session::has('error'))
<div class="alert alert-danger alert-dismissible">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    {!! \Session::get('error') !!}
</div>
@elseif ($errors->any())
<div class="alert alert-warning alert-dismissible">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    {{$errors->first()}}
</div>
@endif
<div class="col-12">
    <div class="card card-body">
        <form method="post" class="row" action="{{route('user_update')}}">
            @csrf
            <div class="col-md-6 mb-3 col-sm-12">
                {!!$data['new_backend']!!}
                {!!$data['backend']!!}
            </div>
            <div class="col-md-6 mb-3 col-sm-12">
                {!!$data['app']!!}
                {!!$data['app_update']!!}
            </div>
            <div class="hr-text mt-4 mb-3 text-primary">User app update notification setup</div>
            <div class="col-md-4">
                <label class="form-label">App update notification type:</label>
                <div class="form-selectgroup">
                    <label class="form-selectgroup-item text-no-wrap">
                        <input type="radio" name="update_type" value="1" class="form-selectgroup-input" @if(env('USER_FORCE_UPDATE')==1) checked @endif>
                        <span class="form-selectgroup-label">Force update</span>
                    </label>
                    <label class="form-selectgroup-item">
                        <input type="radio" name="update_type" value="0" class="form-selectgroup-input" @if(env('USER_FORCE_UPDATE')==0) checked @endif>
                        <span class="form-selectgroup-label">Optional</span>
                    </label>
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label">To the versionCode:</label>
                <input type="text" class="form-control" name="versioncode" value="{{env('USER_VERSIONCODE')}}">
            </div>
            <div class="col-md-4">
                <label class="form-label">.</label>
                <button type="submit" class="btn btn-primary">Send update notification</button>
            </div>
        </form>
    </div>
</div>
<div class="row">
    <div class="col-md-6 col-sm-12">
        <form class="card" method="post" action="{{route('tos_update')}}">
            @csrf
            <div class="card-header">Terms of Services</div>
            <div class="card-body">
                <textarea class="form-control" name="tos" rows="10">{{$data['tos']}}</textarea>
            </div>
            <div class="card-footer">
                <button class="btn btn-primary" type="submit">Update Terms of Service</button>
            </div>
        </form>
    </div>
    <div class="col-md-6 col-sm-12">
        <form class="card" method="post" action="{{route('privacy_update')}}">
            @csrf
            <div class="card-header">Privacy Policy</div>
            <div class="card-body">
                <textarea class="form-control" name="privacy" rows="10">{{$data['privacy']}}</textarea>
            </div>
            <div class="card-footer">
                <button class="btn btn-primary" type="submit">Update Privacy Policy</button>
            </div>
        </form>
    </div>
</div>
@endsection

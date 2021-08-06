@extends('layouts.head')
@section('content')

<div class="row justify-content-center mt-4">
    <div class="col-lg-5 col-md-7 col-10 px-0">
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
        <form method="post" class="card px-0 mt-2" action="{{route('geo_api_update')}}">
            @csrf
            <div class="card-header bg-blue-lt text-dark">
                <span class="card-title">Geo API Setup</span>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">JSON data Provider URL:</label>
                    <input type="text" class="form-control" name="url" placeholder="https://ipapi.co/json/" value="{{old('url', $data['url'])}}">
                </div>
                <div class="mb-3">
                    <label class="form-label">First deep JSON object key:</label>
                    <input type="text" class="form-control" name="deep_link_1" placeholder="leave blank for none" value="{{old('deep_link_1', $data['deep_link_1'])}}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Second deep JSON object key:</label>
                    <input type="text" class="form-control" name="deep_link_2" placeholder="leave blank for none" value="{{old('deep_link_2', $data['deep_link_2'])}}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Country ISO key:</label>
                    <input type="text" class="form-control" name="key" placeholder="country_code" value="{{old('key', $data['key'])}}">
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary btn-block">Update API</button>
            </div>
        </form>
    </div>
</div>
@endsection

@extends('layouts.head')
@section('css')
<style>
    .yt_close {
        position: absolute;
        right: 0;
        top: 0;
        background: rgba(0, 0, 0, 0.5);
        color: #ffffff;
        padding: 3px 10px
    }

    .yt_close:hover {
        color: #ff0000;
    }

</style>
<script type="text/javascript" src="/public/js/jquery-1.11.2.min.js"></script>
@endsection
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
<div class="row">
    <form class="card px-0" method="post" action="{{route('networks_youtube_add')}}">
        @csrf
        <div class="card-header">
            <span class="card-title">Add YouTube video</span>
        </div>
        <div class="card-body row">
            <div class="mb-3 col-md-4 col-sm-12">
                <label class="form-label">Enter a video ID:</label>
                <div class="input-group">
                    <span class="input-group-text pr-1">.../watch?v=</span>
                    <input type="text" class="form-control" name="video_id" placeholder="aSiDu3Ywi8E" value="{{old('video_id')}}">
                </div>
            </div>
            <div class="mb-3 col-md-4 col-sm-12">
                <label class="form-label">Enter reward amount:</label>
                <div class="input-group">
                    <span class="input-group-text">{{env('CURRENCY_NAME')}}s:</span>
                    <input type="text" class="form-control" name="reward_amount" placeholder="10" value="{{old('reward_amount')}}">
                </div>
            </div>
            <div class="col-md-4 col-sm-12">
                <label class="form-label">Country ISOs:</label>
                <input type="text" class="form-control" name="country_iso" placeholder="US,GB,CA" value="{{old('country_iso')}}">
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Add this video</button>
        </div>
    </form>
</div>
<div class="row">
    @foreach($data as $d)
    <div class="col-sm-6 col-md-4 col-xl-3">
        <div class="card d-flex flex-column">
            <div class="text-center">
                <a href="https://www.youtube.com/watch?v={{$d->code}}" target="_blank">
                    <img class="card-img-top" src="https://img.youtube.com/vi/{{$d->code}}/mqdefault.jpg" alt="Youtube Video">
                </a>
                <a href="#" class="yt_close" data-id="{{$d->id}}" data-toggle="modal" data-target="#yt-del" data-backdrop="static" data-keyboard="false">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z"></path>
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </a>
            </div>
            <div class="card-body d-flex flex-column py-2">
                <span class="h4 mb-1 text-truncate">{{$d->title}}</span>
                <div><span class="badge bg-blue">Reward:<span class="badge-addon ">{{$d->points}} {{strtolower(env('CURRENCY_NAME'))}}s</span></span></div>
                <div class="hr-text mt-3 mb-2">Accepted countries</div>
                <span class="small">
                    @php
                    if($d->country == 'all'){
                    echo 'All countries';
                    } else {
                    $ct = explode(',', $d->country);
                    $ctr = '';
                    foreach ($ct as $c) {
                    $ctr .= \Funcs::getCountry($c).', ';
                    }
                    echo rtrim($ctr, ", ");
                    }
                    @endphp
                </span>
            </div>
        </div>
    </div>
    @endforeach
    <div class="col-lg-12 d-flex justify-content-center">
        <ul class="pagination">
            {{ $data->appends(request()->except('page'))->links() }}
        </ul>
    </div>
</div>
<form method="post" action="{{route('networks_youtube_del')}}" class="modal modal-blur fade" id="yt-del" tabindex="-1" role="dialog" aria-hidden="true">
    @csrf
    <input type="hidden" name="id" id="vid-id">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-title">Are you sure?</div>
                <div>You are about to remove this video offer from your database.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary mr-auto" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger">Yes, delete it</button>
            </div>
        </div>
    </div>
</form>
@endsection
@section('javascript')
<script>
    $(document).on("click", ".yt_close", function (ev) {
        ev.preventDefault();
        $("#vid-id").val($(this).data('id'));
    });

</script>
@endsection
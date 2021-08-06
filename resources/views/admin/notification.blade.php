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
    {!!$errors->first()!!}
</div>
@endif
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-auto">
            <h2 class="page-title">Send Push Message</h2>
        </div>
    </div>
</div>
<form method="post" class="row" action="{{route('push_msg_send')}}" enctype="multipart/form-data">
    @csrf
    <div class="col-md-6 col-lg-4">
        <div class="mb-3">
            <label class="form-label">Who you want to send?</label>
            <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column">
                <label class="form-selectgroup-item flex-fill">
                    <input type="radio" id="sendtype-1-radio" name="sendtype" value="1" class="form-selectgroup-input" checked>
                    <div class="form-selectgroup-label d-flex align-items-center p-3">
                        <div class="mr-3">
                            <span class="form-selectgroup-check"></span>
                        </div>
                        <div class="lh-sm">
                            <div class="strong mb-2">Send to single user:</div>
                            <div class="form-check-description">
                                <div class="input-group">
                                    <input id="sendtype-1-input" type="text" name="email_or_userid" class="form-control" @if(isset($data['direct'])) value="{{$data['uid']}}" readonly @else value="{{old('email_or_userid')}}" @endif aria-label="Enter Email or User ID">
                                    <input class="form-control" id="sendtype-1-val" type="hidden" name="email_or_userid_type" value="1">
                                    <button type="button" id="sendtype-1-text" class="btn btn-white dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" @if(isset($data['direct'])) readonly @endif>User ID</button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" id="sendtype-1-opt" data-id="1">By User ID</a>
                                        <a class="dropdown-item" id="sendtype-1-opt" data-id="2">By Email</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </label>
                <label class="form-selectgroup-item flex-fill">
                    <input id="sendtype-2-radio" type="radio" name="sendtype" value="2" class="form-selectgroup-input" @if(isset($data['direct'])) disabled @endif>
                    <div class="form-selectgroup-label d-flex align-items-center p-3">
                        <div class="mr-3">
                            <span class="form-selectgroup-check"></span>
                        </div>
                        <div class="lh-sm">
                            <div class="strong mb-2">To multiple users:</div>
                            <div class="form-check-description">
                                <div class="input-group">
                                    @if(isset($data['direct']))
                                    <span class="input-group-text">From</span>
                                    <input type="text" id="sendtype-2-input" name="sendtype-2-input-from" class="form-control text-center" value="1" disabled>
                                    <span class="input-group-text">to</span>
                                    <input type="text" id="sendtype-2-input" name="sendtype-2-input-to" class="form-control text-center" value="50" disabled>
                                    <span class="input-group-text">users</span>
                                    @else
                                    <span class="input-group-text">From</span>
                                    <input type="text" id="sendtype-2-input" name="sendtype-2-input-from" class="form-control text-center" value="1">
                                    <span class="input-group-text">to</span>
                                    <input type="text" id="sendtype-2-input" name="sendtype-2-input-to" class="form-control text-center" value="50">
                                    <span class="input-group-text">users</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </label>
                <label class="form-selectgroup-item flex-fill">
                    <input type="radio" name="sendtype" value="3" class="form-selectgroup-input" @if(isset($data['direct'])) disabled @endif>
                    <div class="form-selectgroup-label d-flex align-items-center p-3">
                        <div class="mr-3">
                            <span class="form-selectgroup-check"></span>
                        </div>
                        <div class="lh-sm">
                            <div class="strong">Banned users</div>
                            <div class="form-check-description">Last 50 banned users who did not delete the app yet.</div>
                        </div>
                    </div>
                </label>
                <label class="form-selectgroup-item flex-fill">
                    <input type="radio" name="sendtype" value="4" class="form-selectgroup-input" @if(isset($data['direct'])) disabled @endif>
                    <div class="form-selectgroup-label d-flex align-items-center p-3">
                        <div class="mr-3">
                            <span class="form-selectgroup-check"></span>
                        </div>
                        <div class="lh-sm">
                            <div class="strong">Leaderboard winners</div>
                            <div class="form-check-description">Send message to the current leaderboard ranked users.</div>
                        </div>
                    </div>
                </label>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-8">
        <label class="form-label">Message title:</label>
        <div class="input-group mb-3">
            <span class="input-group-text">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z"></path>
                    <path d="M4 21v-13a3 3 0 0 1 3 -3h10a3 3 0 0 1 3 3v6a3 3 0 0 1 -3 3h-9l-4 4"></path>
                    <line x1="8" y1="9" x2="16" y2="9"></line>
                    <line x1="8" y1="13" x2="14" y2="13"></line>
                </svg>
            </span>
            <input type="text" class="form-control" name="title" value="{{old('title')}}" placeholder="Enter a title...">
        </div>
        <div class="col-12 d-flex mb-3">
            <div class="col-6">
                <div class="form-label">Message type</div>
                <div class="form-selectgroup mr-3">
                    <label class="form-selectgroup-item">
                        <input type="radio" name="text_or_multi" value="1" class="form-selectgroup-input" {{ old('text_or_multi', '1') == '1' ? 'checked' : '' }}>
                        <span class="form-selectgroup-label">Text</span>
                    </label>
                    <label class="form-selectgroup-item">
                        <input type="radio" name="text_or_multi" value="2" class="form-selectgroup-input" {{ old('text_or_multi', '1') == '2' ? 'checked' : '' }}>
                        <span class="form-selectgroup-label">Large Image</span>
                    </label>
                    <label class="form-selectgroup-item">
                        <input type="radio" name="text_or_multi" value="3" class="form-selectgroup-input" {{ old('text_or_multi', '1') == '3' ? 'checked' : '' }}>
                        <span class="form-selectgroup-label">Small Image</span>
                    </label>
                </div>
            </div>
            <div class="col-6">
                <div class="form-label">For multimedia message</div>
                <div class="form-file">
                    <input type="file" name="multimedia_image" class="form-file-input img-input" id="imagefile">
                    <label class="form-file-label" for="customFile">
                        <span class="form-file-text img-choose">Choose image...</span>
                        <span class="form-file-button">Browse</span>
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group mb-3">
            <div class="form-label">Message body</div>
            <textarea class="form-control" name="message" data-toggle="autosize" placeholder="Typing something…" style="height:150px;">{{old('message')}}</textarea>
            <button id="form-submit" type="submit" class="btn btn-dark mt-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z"></path>
                    <path d="M21 3L14.5 21a.55 .55 0 0 1 -1 0L10 14L3 10.5a.55 .55 0 0 1 0 -1L21 3"></path>
                </svg>
                @if (\Session::has('success'))
                Send another
                @else
                Send message
                @endif
            </button>
        </div>
    </div>
</form>
@endsection

@section('css')
<script type="text/javascript" src="/public/js/jquery-1.11.2.min.js"></script>
@endsection
@section('javascript')
<script>
    $(document).on("click", "#sendtype-1-input", function () {
        $('input:radio[id=sendtype-1-radio]').click();
    });
    $(document).on("click", "#sendtype-2-input", function () {
        $('input:radio[id=sendtype-2-radio]').click();
    });
    $(document).on("click", "#sendtype-1-opt", function () {
        var id = $(this).data('id');
        if (id == 1) {
            $("#sendtype-1-text").text('User ID');
            $("#sendtype-1-val").val('1');
        } else {
            $("#sendtype-1-text").text('Email');
            $("#sendtype-1-val").val('2');
        }
    });
    $('.img-input').on('change', function () {
        var fileName = $(this).val().split('\\').pop();
        $(this).closest('.form-file').find('.img-choose').addClass("selected").text(fileName);
    });

</script>
@endsection

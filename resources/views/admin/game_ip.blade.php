@extends('layouts.head')
@section('css')
<style>
    .fixed-height {
        height: 250px !important;
    }

    .pointer {
        cursor: pointer;
    }

</style>
<script type="text/javascript" src="/public/js/jquery-1.11.2.min.js"></script>
@endsection
@section('content')
<div class="p-3 d-flex bg-gray-lt text-dark">
    <div>
        <span class="h3 mr-1">{{$data['cat']}}</span>
        <span>({{$data['size']}})</span>
    </div>
    <span class="badge ml-auto bg-blue px-3 py-1">Time:<span class="badge-addon bg-blue-lt">{{$data['time']}} seconds</span></span>
</div>
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


<div class="row mt-4">
    <div class="col-lg-3 col-md-4 col-sm-6 pb-4">
        <form id="imgForm" class="bg-gray-lt card fixed-height d-flex justify-content-center text-center" method="post" action="{{route('game_ip_add')}}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{$data['id']}}" />
            <input type="file" name="image" class="d-none" id="imagefile">
            <label class="pointer" for="imagefile">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-xl" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z"></path>
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
            </label>
        </form>
    </div>
    @foreach($data['q'] as $d)
    <div class="col-lg-3 col-md-4 col-sm-6 pb-4">
        <a href="#" class="btn-edit card d-block text-center" data-id="{{$d->id}}" data-toggle="modal" data-target="#ip-edit" data-backdrop="static" data-keyboard="false">
            <img src="{{$d->image}}" class="fixed-height w-100 rounded" />
        </a>
    </div>
    @endforeach
    <div class="col-lg-12 d-flex justify-content-center mt-3">
        <ul class="pagination">
            {{ $data['q']->appends(request()->except('page'))->links() }}
        </ul>
    </div>
</div>
<div class="modal modal-blur fade" id="ip-edit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content card">
            <div class="modal-header">
                <h5 class="modal-title">Update your image</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z"></path>
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <div class="card-body pt-3">
                <form method="post" action="{{route('game_ip_edit')}}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="qs_id">
                    <div class="mb-4 form-file">
                        <input type="file" name="update_image" class="form-file-input add-file-input" id="addFile">
                        <label class="form-file-label" for="addFile">
                            <span class="form-file-text add-file-choose">Choose an image...</span>
                            <span class="form-file-button">Browse</span>
                        </label>
                    </div>
                    <button type="submit" class="btn btn-block btn-primary">Update</button>
                </form>
                <form method="post" action="{{route('game_ip_del')}}">
                    @csrf
                    <input type="hidden" name="id" id="qs_del_id">
                    <button type="submit" class="btn mt-2 btn-block btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('javascript')
<script>
    $('#imagefile').change(function () {
        $('#imgForm').submit();
    });
    $('.add-file-input').on('change', function () {
        var fileName = $(this).val().split('\\').pop();
        $(this).closest('.form-file').find('.add-file-choose').addClass("selected").text(fileName);
    });
    $(document).on("click", ".btn-del", function (ev) {
        ev.preventDefault();
        $("#qs-id").val($(this).data('id'));
    });
    $(document).on("click", ".btn-edit", function (ev) {
        ev.preventDefault();
        $("#qs_id").val($(this).data('id'));
        $("#qs_del_id").val($(this).data('id'));
    });

</script>
@endsection

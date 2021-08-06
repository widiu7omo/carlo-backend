@extends('layouts.head')
@section('css')
<script type="text/javascript" src="{{asset("js/jquery-1.11.2.min.js")}}"></script>
<style>
    .fixed-img-height {
        height: 200px !important;
        display: block;
        margin: 0 auto;
    }

    .fixed-img-bottom {
        background-color: rgba(0, 0, 0, 0.7);
        padding: 12px 10px 5px 10px;
        position: absolute;
        bottom: 0;
        color: #ffffff;
    }

    .btns {
        position: absolute;
        top: 0;
        right: 0;
        padding: 3px 10px;
        background-color: rgba(0, 0, 0, 0.5);
        color: #ffffff
    }

    .btn-edit,
    .btn-close {
        color: #ffffff;
        padding: 0px 5px;
    }

</style>
@endsection
@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-auto">
            <h2 class="page-title">Gateway Setup</h2>
        </div>
    </div>
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
    {!!$errors->first()!!}
</div>
@endif
<div class="row">
    <form class="col-xl-4 col-md-5 col-sm-12" action="{{route('gateway_category_add')}}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="card">
            <div class="card-header bg-gray-lt pt-3 pb-2">
                <h4 class="text-dark">Add gift category</h4>
            </div>
            <div class="card-body pt-2 row">
                <div class="mb-3">
                    <label class="form-label">Gift category name</label>
                    <input type="text" class="form-control" name="name" placeholder="Amazon Gift Card" value="{{old('name')}}">
                </div>
                <div class="mb-3">
                    <div class="form-label">Item image</div>
                    <div class="form-file">
                        <input type="file" name="image" class="form-file-input modal-img-input" id="customFile">
                        <label class="form-file-label" for="customFile">
                            <span class="form-file-text modal-img-choose">Choose an image...</span>
                            <span class="form-file-button">Browse</span>
                        </label>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirmation dialog text:</label>
                    <input type="text" class="form-control" name="input_desc" placeholder="Enter your email address to receive the gift card" value="{{old('input_desc')}}">
                </div>
                <div class="mb-3">
                    <div class="form-label">Input box type:</div>
                    <select class="form-select" name="input_type">
                        <option value="1">Text</option>
                        <option value="2" selected>Email</option>
                        <option value="3">Number</option>
                    </select>
                </div>
                <div>
                    <label class="form-label" name="country">For countries:</label>
                    <input type="text" class="form-control" name="country" placeholder="Amazon GC" value="{{old('country')}}">
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-dark">Add this item</button>
            </div>
        </div>
    </form>
    <div class="col-lg-8 col-md-7 col-sm-12">
        <div class="alert alert-info">Click on a category to administer its items.</div>
        <div class="row">
            @foreach($data as $c)
            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="card">
                    <a href="{{route('gateway', ['id' => $c->id])}}" class="d-block bg-gray-lt">
                        <img src="{{$c->image}}" class="fixed-img-height w-100">
                    </a>
                    <div class="btns">
                        <a href="#" class="btn-close" data-id="{{$c->id}}" data-toggle="modal" data-target="#cat-del" data-backdrop="static" data-keyboard="false">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" />
                                <line x1="4" y1="7" x2="20" y2="7" />
                                <line x1="10" y1="11" x2="10" y2="17" />
                                <line x1="14" y1="11" x2="14" y2="17" />
                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                        </a>
                        <a href="#" class="btn-edit" data-id="{{$c->id}}" data-name="{{$c->name}}" data-desc="{{$c->input_desc}}" data-ty="{{$c->input_type}}" data-typ="{{['','Text','Email','Number'][$c->input_type]}}" data-ct="{{$c->country == 'all' ? '' : $c->country}}" data-toggle="modal" data-target="#cat-edit" data-backdrop="static" data-keyboard="false">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" />
                                <path d="M9 7 h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3" />
                                <path d="M9 15h3l8.5 -8.5a1.5 1.5 0 0 0 -3 -3l-8.5 8.5v3" />
                                <line x1="16" y1="5" x2="19" y2="8" /></svg>
                        </a>
                    </div>
                    <div class="fixed-img-bottom w-100">
                        <div class="h4 text-center">{{$c->name}}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="col-lg-12 d-flex justify-content-center mt-3">
            <ul class="pagination">
                {{ $data->appends(request()->except('page'))->links() }}
            </ul>
        </div>
    </div>
</div>
<form method="post" action="{{route('gateway_category_del')}}" class="modal modal-blur fade" id="cat-del" tabindex="-1" role="dialog" aria-hidden="true">
    @csrf
    <input type="hidden" name="id" id="cat-id">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-title">Are you sure?</div>
                <div>You are about to remove this category and all of its questions from your database.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary mr-auto" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger">Yes, delete it</button>
            </div>
        </div>
    </div>
</form>
<form method="post" action="{{route('gateway_category_edit')}}" enctype="multipart/form-data" class="modal modal-blur fade" id="cat-edit" tabindex="-1" role="dialog" aria-hidden="true">
    @csrf
    <input type="hidden" name="id" id="edit-id">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content card">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Gift category name</label>
                    <input type="text" class="form-control" name="name" id="edit-name">
                </div>
                <div class="mb-3">
                    <div class="form-label">Item image</div>
                    <div class="form-file">
                        <input type="file" name="image" class="form-file-input modal-img-input" id="customFile">
                        <label class="form-file-label" for="customFile">
                            <span class="form-file-text modal-img-choose">Choose an image...</span>
                            <span class="form-file-button">Browse</span>
                        </label>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirmation dialog text:</label>
                    <input type="text" class="form-control" name="input_desc" id="edit-desc">
                </div>
                <div class="mb-3">
                    <div class="form-label">Input box type:</div>
                    <select class="form-select" name="input_type">
                        <option id="edit-ty" selected></option>
                        <option value="1">Text</option>
                        <option value="2">Email</option>
                        <option value="3">Number</option>
                    </select>
                </div>
                <div>
                    <label class="form-label" name="country">For countries:</label>
                    <input type="text" class="form-control" name="country" id="edit-ct">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary mr-auto" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Update category</button>
            </div>
        </div>
    </div>
</form>
@endsection

@section('javascript')
<script>
    $('.modal-img-input').on('change', function () {
        var fileName = $(this).val().split('\\').pop();
        $(this).closest('.form-file').find('.modal-img-choose').addClass("selected").text(fileName);
    });
    $(document).on("click", ".btn-close", function (ev) {
        ev.preventDefault();
        $("#cat-id").val($(this).data('id'));
    });
    $(document).on("click", ".btn-edit", function (ev) {
        ev.preventDefault();
        $("#edit-id").val($(this).data('id'));
        $("#edit-name").val($(this).data('name'));
        $("#edit-desc").val($(this).data('desc'));
        $("#edit-ty").val($(this).data('ty'));
        $("#edit-ty").text($(this).data('typ'));
        $("#edit-ct").val($(this).data('ct'));
    });
</script>
@endsection

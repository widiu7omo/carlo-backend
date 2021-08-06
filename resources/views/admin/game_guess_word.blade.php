@extends('layouts.head')
@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-auto">
            <h2 class="page-title">Word Guessing</h2>
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
    <div class="col-xl-4 col-md-6 col-sm-12">
        <form action="{{route('game_guess_word_add')}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="card">
                <div class="card-header bg-gray-lt pt-3 pb-2">
                    <h4 class="text-dark">Add a word</h4>
                </div>
                <div class="card-body pt-2">
                    <div class="mb-3">
                        <label class="form-label">Word name:</label>
                        <input type="text" class="form-control" name="word_name" placeholder="Apple" value="{{old('word_name')}}">
                    </div>
                    <div class="mb-3">
                        <div class="form-label">Image hint:</div>
                        <div class="form-file">
                            <input type="file" name="item_image" class="form-file-input add-file-input" id="addFile">
                            <label class="form-file-label" for="addFile">
                                <span class="form-file-text add-file-choose">Choose an image...</span>
                                <span class="form-file-button">Browse</span>
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Additional info:</label>
                        <input type="text" class="form-control" name="word_info" placeholder="Guess the name of this fruit." value="{{old('word_info')}}">
                    </div>
                    <div>
                        <label class="form-label">Allowed countries:</label>
                        <input type="text" class="form-control" name="country" placeholder="US,UK,CA" value="{{old('country')}}">
                    </div>
                    <div>
                        <label class="form-label">Time up:</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="time" placeholder="30" value="{{old('time')}}">
                            <span class="input-group-text">seconds</span>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-dark">Add this item</button>
                </div>
            </div>
        </form>
        <div class="card card-body">
            <a href="#" data-toggle="modal" data-target="#gw-set" data-backdrop="static" data-keyboard="false" class="btn btn-block btn-secondary">Game settings</a>
        </div>
    </div>
    <div class="col-xl-8 col-md-6 col-sm-12">
        <div class="card">
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Info</th>
                            <th>Word</th>
                            <th colspan=2></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $d)
                        <tr>
                            <td><img class="avatar avatar-md mr-3 rounded" src="{{$d->image}}" /></td>
                            <td>{{$d->info}}</td>
                            <td>
                                @php
                                $r = str_split($d->word);
                                foreach($r as $a){
                                echo '<span class="btn btn-sm btn-primary mr-1 mb-1">'.$a.'</span>';
                                }
                                @endphp
                            </td>
                            <td>
                                <a href="#" class="btn-close" data-id="{{$d->id}}" data-toggle="modal" data-target="#gw-del" data-backdrop="static" data-keyboard="false">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" />
                                        <line x1="4" y1="7" x2="20" y2="7" />
                                        <line x1="10" y1="11" x2="10" y2="17" />
                                        <line x1="14" y1="11" x2="14" y2="17" />
                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                </a>
                            </td>
                            <td>
                                <a href="#" class="btn-edit" data-id="{{$d->id}}" data-word="{{$d->word}}" data-infos="{{$d->info}}" data-country="{{$d->country == 'all' ? '' : $d->country}}" data-time="{{$d->max_time}}" data-toggle="modal" data-target="#gw-edit" data-backdrop="static" data-keyboard="false">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" />
                                        <path d="M9 7 h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3" />
                                        <path d="M9 15h3l8.5 -8.5a1.5 1.5 0 0 0 -3 -3l-8.5 8.5v3" />
                                        <line x1="16" y1="5" x2="19" y2="8" /></svg>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer d-flex align-items-center">
                <p class="m-0 text-muted">Showing <span>{{$data->firstItem()}}</span> to <span>{{$data->lastItem()}}</span> of <span>{{$data->total()}}</span> entries</p>
                <ul class="pagination m-0 ml-auto">
                    {{ $data->appends(request()->except('page'))->links() }}
                </ul>
            </div>
        </div>
    </div>
</div>

<form method="post" action="{{route('game_guess_word_settings_update')}}" class="modal modal-blur fade" id="gw-set" tabindex="-1" role="dialog" aria-hidden="true">
    @csrf
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content card">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Reward amount:</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="reward_amount" value="{{env('GW_REWARD')}}">
                        <span class="input-group-text">{{strtolower(env('CURRENCY_NAME'))}}s</span>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Retry chance per day:</label>
                    <input type="text" class="form-control" name="retry_chance" value="{{env('GW_RETRY_CHANCE')}}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Extra retry cost:</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="retry_cost" value="{{env('GW_RETRY_COST')}}">
                        <span class="input-group-text">{{strtolower(env('CURRENCY_NAME'))}}s</span>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Free letter help per day:</label>
                    <input type="text" class="form-control" name="hint_chance" value="{{env('GW_HINT_CHANCE')}}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Extra letter help cost:</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="hint_cost" value="{{env('GW_HINT_COST')}}">
                        <span class="input-group-text">{{strtolower(env('CURRENCY_NAME'))}}s</span>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Solving cost:</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="solve_cost" value="{{env('GW_SOLVE_COST')}}">
                        <span class="input-group-text">{{strtolower(env('CURRENCY_NAME'))}}s</span>
                    </div>
                </div>
                <div>
                    <label class="form-label">Time offset:</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="offset" value="{{env('GW_TIME_OFFSET')}}">
                        <span class="input-group-text">seconds</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary mr-auto" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Update settings</button>
            </div>
        </div>
    </div>
</form>
<form method="post" action="{{route('game_guess_word_del')}}" class="modal modal-blur fade" id="gw-del" tabindex="-1" role="dialog" aria-hidden="true">
    @csrf
    <input type="hidden" name="id" id="gw-id">
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
<form method="post" action="{{route('game_guess_word_edit')}}" class="modal modal-blur fade" id="gw-edit" tabindex="-1" role="dialog" aria-hidden="true" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="id" id="mod-id">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content card">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Word name:</label>
                    <input type="text" class="form-control" name="word_name" id="mod-word">
                </div>
                <div class="mb-3">
                    <div class="form-label">Image hint:</div>
                    <div class="form-file">
                        <input type="file" name="item_image" class="form-file-input add-file-input" id="addFile">
                        <label class="form-file-label" for="addFile">
                            <span class="form-file-text add-file-choose">Choose an image...</span>
                            <span class="form-file-button">Browse</span>
                        </label>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Additional info:</label>
                    <input type="text" class="form-control" name="word_info" id="mod-info">
                </div>
                <div>
                    <label class="form-label">Allowed countries:</label>
                    <input type="text" class="form-control" name="country" id="mod-country">
                </div>
                <div>
                    <label class="form-label">Time up:</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="time" id="mod-time">
                        <span class="input-group-text">seconds</span>
                    </div>
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
@section('css')
<script type="text/javascript" src="/public/js/jquery-1.11.2.min.js"></script>
@endsection
@section('javascript')
<script>
    $('.add-file-input').on('change', function () {
        var fileName = $(this).val().split('\\').pop();
        $(this).closest('.form-file').find('.add-file-choose').addClass("selected").text(fileName);
    });
    $(document).on("click", ".btn-close", function (ev) {
        ev.preventDefault();
        $("#gw-id").val($(this).data('id'));
    });
    $(document).on("click", ".btn-edit", function (ev) {
        ev.preventDefault();
        $("#mod-id").val($(this).data('id'));
        $("#mod-word").val($(this).data('word'));
        $("#mod-info").val($(this).data('infos'));
        $("#mod-country").val($(this).data('country'));
        $("#mod-time").val($(this).data('time'));
    });

</script>
@endsection

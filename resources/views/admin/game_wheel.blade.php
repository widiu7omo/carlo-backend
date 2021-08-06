@extends('layouts.head')
@section('content')
<div class="px-3 py-1 mb-3 d-flex bg-blue-lt rounded text-dark align-items-center"><span class="h3 mt-1">Spin wheel game</span></div>
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
    <div class="col-lg-4 col-md-5 col-sm-12">
        <form class="card card-body" method="post" action="{{route('game_wheel_settings')}}">
            @csrf
            <div class="mb-2">
                <label class="form-label">Daily round limit:</label>
                <input type="text" class="form-control" name="wheel_limit" value="{{env('WHEEL_DAILY_LIMIT')}}">
            </div>
            <div class="d-flex">
                <div class="mb-3 flex-fill">
                    <label class="form-label">Round cost:</label>
                    <input type="text" class="form-control" name="wheel_cost" value="{{env('WHEEL_ROUND_COST')}}">
                </div>
                <div>
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn ml-3 px-4 btn-primary">Update</button>
                </div>
            </div>
        </form>
        <form class="card px-0" method="post" action="{{route('game_wheel_add')}}">
            @csrf
            <input type="hidden" id="type" name="type" value="{{old('type', '1')}}" />
            <div class="card-header bg-blue-lt text-dark">
                <span class="card-title">Add wheel item</span>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="mb-3 card-tabs">
                        <ul class="nav nav-tabs">
                            <li class="nav-item"><a href="#t-1" id="g_coin" class="nav-link @if(old('type', '1') == '1') active @endif" data-toggle="tab">{{env('CURRENCY_NAME')}}s</a></li>
                            <li class="nav-item"><a href="#t-2" id="g_card" class="nav-link @if(old('type', '1') == '2') active @endif" data-toggle="tab">Scratch Card</a></li>
                        </ul>
                        <div class="tab-content">
                            <div id="t-1" class="card card-body tab-pane @if(old('type', '1') == '1') show active @endif">
                                <div class="d-flex">
                                    <div class="mr-2 flex-fill">
                                        <label class="form-label">{{env('CURRENCY_NAME')}} amount:</label>
                                        <input type="text" class="form-control" name="coin" placeholder="30" value="{{old('amount')}}">
                                    </div>
                                    <div class="ml-auto">
                                        <label class="form-label text-nowrap">BG Color:</label>
                                        <input type="color" class="form-control form-control-color" name="coin_color" value="#6f21e7" title="Choose your color">
                                    </div>
                                </div>
                            </div>
                            <div id="t-2" class="card card-body tab-pane @if(old('type', '1') == '2') show active @endif">
                                <div class="d-flex">
                                    <div class="mr-2 flex-fill">
                                        <label class="form-label">Choose a card:</label>
                                        <div class="input-group">
                                            <select name="card" class="form-select">
                                                @foreach($data['c'] as $c)
                                                <option value="{{$c->id}}">{{$c->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="ml-auto">
                                        <label class="form-label text-nowrap">BG Color:</label>
                                        <input type="color" class="form-control form-control-color" name="card_color" value="#6f21e7" title="Choose your color">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="d-flex"><label class="form-label">Difficulty level:</label><span class="ml-2" id="seekbaroutput">{{old('difficulty', 2)}}</span></div>
                    <input name="difficulty" class="form-range" oninput="sliderChange(this.value)" type="range" min="0" max="5" value="{{old('difficulty', 2)}}" step="1" class="slider">
                </div>
                <div class="mb-0">
                    <label class="form-label">Success message:</label>
                    <input type="text" class="form-control" name="message" placeholder="Congrats! You won 30 {{strtolower(env('CURRENCY_NAME'))}}s" value="{{old('message')}}">
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Add this item</button>
            </div>
        </form>
    </div>
    <div class="col-lg-8 col-md-7 col-sm-12">
        <div class="row">
            @if(env('WHEEL_FREE_CHANCE') == 1)
            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="card card-body p-2">
                    <div class="d-flex">
                        <div style="background-color:{{$data['f']['bg']}}" class="flex-fill text-center p-3 text-white font-weight-bold">{{$data['f']['text']}}</div>
                        <a class="px-2 align-self-center d-block" href="{{route('game_wheel_replace')}}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z"></path>
                                <rect x="3" y="3" width="6" height="6" rx="1"></rect>
                                <rect x="15" y="15" width="6" height="6" rx="1"></rect>
                                <path d="M21 11v-3a2 2 0 0 0 -2 -2h-6l3 3m0 -6l-3 3"></path>
                                <path d="M3 13v3a2 2 0 0 0 2 2h6l-3 -3m0 6l3 -3"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            @else
            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="card card-body p-2">
                    <div class="d-flex">
                        <div style="background-color:{{$data['f']['bg']}}" class="flex-fill text-center p-3 text-white font-weight-bold">{{$data['f']['text']}}</div>
                        <div class="px-2">
                            <a class="d-block w-edit text-blue cursor-pointer" data-cid="{{$data['f']['card_id']}}" data-id="{{$data['f']['id']}}" data-text="{{$data['f']['text']}}" data-bg="{{$data['f']['bg']}}" data-sb="{{$data['f']['difficulty']}}" data-msg="{{$data['f']['message']}}" data-toggle="modal" data-target="#w-edit" data-backdrop="static" data-keyboard="false">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" />
                                    <path d="M9 7 h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3" />
                                    <path d="M9 15h3l8.5 -8.5a1.5 1.5 0 0 0 -3 -3l-8.5 8.5v3" />
                                    <line x1="16" y1="5" x2="19" y2="8" /></svg>
                            </a>
                            <a href="{{route('game_wheel_replace')}}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z"></path>
                                    <rect x="3" y="3" width="6" height="6" rx="1"></rect>
                                    <rect x="15" y="15" width="6" height="6" rx="1"></rect>
                                    <path d="M21 11v-3a2 2 0 0 0 -2 -2h-6l3 3m0 -6l-3 3"></path>
                                    <path d="M3 13v3a2 2 0 0 0 2 2h6l-3 -3m0 6l3 -3"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @foreach($data['o'] as $d)
            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="card card-body p-2">
                    <div class="d-flex">
                        <div style="background-color:{{$d->bg}}" class="flex-fill text-center p-3 text-white font-weight-bold">{{$d->text}}</div>
                        <div class="px-2">
                            <a class="d-block w-edit text-blue cursor-pointer" data-cid="{{$d->card_id}}" data-id="{{$d->id}}" data-text="{{$d->text}}" data-bg="{{$d->bg}}" data-sb="{{$d->difficulty}}" data-msg="{{$d->message}}" data-toggle="modal" data-target="#w-edit" data-backdrop="static" data-keyboard="false">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" />
                                    <path d="M9 7 h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3" />
                                    <path d="M9 15h3l8.5 -8.5a1.5 1.5 0 0 0 -3 -3l-8.5 8.5v3" />
                                    <line x1="16" y1="5" x2="19" y2="8" /></svg>
                            </a>
                            <a class="w-del text-red cursor-pointer" data-id="{{$d->id}}" data-toggle="modal" data-target="#w-del" data-backdrop="static" data-keyboard="false">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" />
                                    <line x1="4" y1="7" x2="20" y2="7" />
                                    <line x1="10" y1="11" x2="10" y2="17" />
                                    <line x1="14" y1="11" x2="14" y2="17" />
                                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
<form method="post" action="{{route('game_wheel_del')}}" class="modal modal-blur fade" id="w-del" tabindex="-1" role="dialog" aria-hidden="true">
    @csrf
    <input type="hidden" name="id" id="w-id">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-title">Are you sure?</div>
                <div>You are about to remove this item from your database.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary mr-auto" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger">Yes, delete it</button>
            </div>
        </div>
    </div>
</form>
<form method="post" action="{{route('game_wheel_edit')}}" class="modal modal-blur fade" id="w-edit" tabindex="-1" role="dialog" aria-hidden="true">
    @csrf
    <input type="hidden" name="id" id="we-id">
    <input type="hidden" id="modal_type" name="type" value="{{old('type', '1')}}" />
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content card">
            <div class="card-header bg-primary text-white">Update wheel item</div>
            <div class="card-body">
            <div class="mb-3">
                    <div class="mb-3 card-tabs">
                        <ul class="nav nav-tabs">
                            <li class="nav-item"><a href="#mt-1" id="m_coin" class="nav-link" data-toggle="tab">{{env('CURRENCY_NAME')}}s</a></li>
                            <li class="nav-item"><a href="#mt-2" id="m_card" class="nav-link" data-toggle="tab">Scratch Card</a></li>
                        </ul>
                        <div class="tab-content">
                            <div id="mt-1" class="card card-body tab-pane">
                                <div class="d-flex">
                                    <div class="mr-2 flex-fill">
                                        <label class="form-label">{{env('CURRENCY_NAME')}} amount:</label>
                                        <input type="text" class="form-control" name="coin" placeholder="30" value="{{old('amount')}}">
                                    </div>
                                    <div class="ml-auto">
                                        <label class="form-label text-nowrap">BG Color:</label>
                                        <input type="color" class="form-control form-control-color" name="coin_color" value="#6f21e7" title="Choose your color">
                                    </div>
                                </div>
                            </div>
                            <div id="mt-2" class="card card-body tab-pane">
                                <div class="d-flex">
                                    <div class="mr-2 flex-fill">
                                        <label class="form-label">Choose a card:</label>
                                        <div class="input-group">
                                            <select name="card" class="form-select">
                                                @foreach($data['c'] as $c)
                                                <option value="{{$c->id}}">{{$c->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="ml-auto">
                                        <label class="form-label text-nowrap">BG Color:</label>
                                        <input type="color" class="form-control form-control-color" name="card_color" value="#6f21e7" title="Choose your color">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="d-flex"><label class="form-label">Difficulty level:</label><span class="ml-2" id="seekbar-modal">3</span></div>
                    <input name="difficulty" class="form-range" oninput="modalChange(this.value)" type="range" min="0" max="5" value="5" id="w-sb" step="1" class="slider">
                </div>
                <div class="mb-0">
                    <label class="form-label">Success message:</label>
                    <input type="text" class="form-control" name="message" id="w-msg">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary mr-auto" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Update</button>
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
    $(document).on("click", ".w-del", function (ev) {
        ev.preventDefault();
        $("#w-id").val($(this).data('id'));
    });
    $(document).on("click", ".w-edit", function (ev) {
        ev.preventDefault();
        $("#we-id").val($(this).data('id'));
        $("#w-text").val($(this).data('text'));
        $("#w-bg").val($(this).data('bg'));
        $("#w-msg").val($(this).data('msg'));
        $("#w-sb").val($(this).data('sb'));
        $("#seekbar-modal").text($(this).data('sb'));
        if($(this).data('cid') == 0){
            $("#m_coin").addClass("active");
            $("#m_card").removeClass("active");
            $("#mt-1").addClass("active");
            $("#mt-2").removeClass("active");
            $("#modal_type").val("1");
        } else {
            $("#m_card").addClass("active");
            $("#m_coin").removeClass("active");
            $("#mt-2").addClass("active");
            $("#mt-1").removeClass("active");
            $("#modal_type").val("2");
        }
    });

    function sliderChange(val) {
        document.getElementById('seekbaroutput').innerHTML = val;
    }

    function modalChange(val) {
        document.getElementById('seekbar-modal').innerHTML = val;
    }
    $('#g_coin').click(function () {
        $("#type").val("1");
    });
    $('#g_card').click(function () {
        $("#type").val("2");
    });
    $('#m_coin').click(function () {
        $("#modal_type").val("1");
    });
    $('#m_card').click(function () {
        $("#modal_type").val("2");
    });

</script>
@endsection

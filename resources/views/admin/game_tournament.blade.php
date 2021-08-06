@extends('layouts.head')
@push('style')
    <style>
        .list-inline-item {
            margin-right: 20px !important;
        }

        .item-btn1 {
            position: absolute;
            top: 5px;
            right: 50px
        }

        .item-btn2 {
            color: red !important;
            position: absolute;
            top: 5px;
            right: 20px
        }

    </style>
    <link rel="stylesheet" type="text/css" href="{{asset("css/jquery.datetimepicker.min.css")}}"/>
@endpush
@section('content')
    <div class="px-3 py-1 mb-3 d-flex bg-blue-lt rounded text-dark align-items-center">
        <span class="h3 mt-1">Quiz Tournament</span>
        <div class="ml-auto">
            <a href="#" class="btn-edit btn btn-sm btn-primary">Previous Winners</a>
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
            {{$errors->first()}}
        </div>
    @endif


    <div class="row">
        <div class="col-lg-4 col-md-5 col-sm-12">
            <div class="row">
                <div class="col-md-6 col-sm-12 mb-3">
                    <a href="#" class="btn btn-edit btn-block btn-primary" data-id="-1" data-qs="" data-op=""
                       data-ans="" data-time="" data-sc="" data-toggle="modal" data-target="#qs-edit"
                       data-backdrop="static" data-keyboard="false">Add a question</a>
                </div>
                <div class="col-md-6 col-sm-12 mb-3">
                    <a href="#" class="btn btn-del btn-block btn-danger" data-id="-1" data-toggle="modal"
                       data-target="#qs-del" data-backdrop="static" data-keyboard="false">Delete all questions</a>
                </div>
            </div>
            <form class="card px-0" method="post" action="{{route('game_tour_sett')}}">
                @csrf
                <div class="card-header bg-blue-lt text-dark px-3 py-2 font-weight-bold">Tournament Settings</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Tournament name:</label>
                        <input type="text" class="form-control" name="name" placeholder="Quiz Tournament"
                               value="{{old('name', $data['s']['name'])}}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tournament will begin on:</label>
                        <input type="text" class="form-control" name="begin_time" id="begin-date"
                               placeholder="h:m dd/mm/yyyy" value="{{old('begin_time', $data['s']['begin_time'])}}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Result will be published after:</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="publish_time" placeholder="30"
                                   value="{{old('publish_time', $data['s']['publish_time'])}}">
                            <span class="input-group-text">minutes</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Result will show until:</label>
                        <input type="text" class="form-control" name="result_time" id="result-until"
                               placeholder="h:m dd/mm/yyyy" value="{{old('result_time', $data['s']['result_time'])}}">
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <label class="form-label">Entry fee:</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="fee" placeholder="30"
                                       value="{{old('fee', $data['s']['fee'])}}">
                                <span class="input-group-text">{{strtolower(env('CURRENCY_NAME'))}}s</span>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <label class="form-label">Total reward:</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="reward" placeholder="5000"
                                       value="{{old('reward', $data['s']['reward'])}}">
                                <span class="input-group-text">{{strtolower(env('CURRENCY_NAME'))}}s</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Schedule tournament</button>
                </div>
            </form>
            <form class="card card-body" method="get" action="{{route('game_tour_winner_form')}}">
                <div class="d-flex">
                    <div class="w-100">
                        <label class="form-label">Set total winners:</label>
                        <input type="text" class="form-control" name="total_winners"
                               value="{{env('TOUR_WINNER_COUNT')}}">
                    </div>
                    <div>
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn ml-3 px-4 btn-primary">Update</button>
                    </div>
                </div>
            </form>
        </div>


        <div class="col-lg-8 col-md-7 col-sm-12">
            <div class="row">
                @foreach($data['q'] as $d)
                    <div class="col-lg-6 col-md-12">
                        <div class="card card-body mb-2">
                            <div class="font-weight-bold">
                                {{$d->question}}
                            </div>
                            <a href="#" class="item-btn1 btn-edit" data-id="{{$d->id}}" data-qs="{{$d->question}}"
                               data-op="{!!str_replace(';;','&#10;', $d->options)!!}" data-ans="{{$d->answer}}"
                               data-time="{{$d->time}}" data-sc="{{$d->score}}" data-toggle="modal"
                               data-target="#qs-edit" data-backdrop="static" data-keyboard="false">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24"
                                     viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z"/>
                                    <path d="M9 7 h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3"/>
                                    <path d="M9 15h3l8.5 -8.5a1.5 1.5 0 0 0 -3 -3l-8.5 8.5v3"/>
                                    <line x1="16" y1="5" x2="19" y2="8"/>
                                </svg>
                            </a>
                            <a href="#" class="item-btn2 btn-del" data-id="{{$d->id}}" data-toggle="modal"
                               data-target="#qs-del" data-backdrop="static" data-keyboard="false">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24"
                                     viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z"/>
                                    <line x1="4" y1="7" x2="20" y2="7"/>
                                    <line x1="10" y1="11" x2="10" y2="17"/>
                                    <line x1="14" y1="11" x2="14" y2="17"/>
                                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/>
                                </svg>
                            </a>
                            <div class="hr-text hr-text-right my-3 font-weight-bold text-blue">{{$d->time}} seconds
                                &bull;&bull;&bull;&bull; {{$d->score}} {{strtolower(env('CURRENCY_NAME'))}}s
                            </div>
                            <ul class="list-inline">
                                @php
                                    $zz = explode(';;', $d->options);
                                    for($j = 0; $j < count($zz); $j++){ if($j==$d->answer - 1){
                                        echo '<li class="text-green"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z"></path>
                                                <path d="M7 12l5 5l10 -10"></path>
                                                <path d="M2 12l5 5m5 -5l5 -5"></path>
                                            </svg> '. $zz[$j] .'</li>';
                                        } else {
                                        echo '<li><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z"></path>
                                                <polyline points="7 7 12 12 7 17"></polyline>
                                                <polyline points="13 7 18 12 13 17"></polyline>
                                            </svg> '. $zz[$j] .'</li>';
                                        }
                                        }
                                @endphp
                            </ul>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="col-lg-12 d-flex justify-content-center mt-3">
                <ul class="pagination">
                    {{ $data['q']->appends(request()->except('page'))->links() }}
                </ul>
            </div>
        </div>

    </div>
    <form method="post" action="{{route('game_tour_qs_del')}}" class="modal modal-blur fade" id="qs-del" tabindex="-1"
          role="dialog" aria-hidden="true">
        @csrf
        <input type="hidden" name="id" id="qs-id">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="modal-title">Are you sure?</div>
                    <div>You are about to remove this questions from your database.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary mr-auto" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Yes, delete it</button>
                </div>
            </div>
        </div>
    </form>
    <form method="post" action="{{route('game_tour_qs_add')}}" class="modal modal-blur fade" id="qs-edit" tabindex="-1"
          role="dialog" aria-hidden="true">
        @csrf
        <input type="hidden" name="id" id="qs_id">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content card">
                <div class="card-header bg-primary text-white">Add / update a question</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Question:</label>
                        <input type="text" class="form-control" placeholder="What is the capital of United States?"
                               name="question" id="qs_qs">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Options:</label>
                        <textarea class="form-control" name="options" rows="4"
                                  placeholder="California&#10;New York&#10;Washington, D.C.&#10;Arizona"
                                  id="qs_op"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Which line is correct answer?</label>
                        <input type="text" class="form-control" placeholder="3" name="answer" id="qs_ans">
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <label class="form-label">Answer time limit:</label>
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="30" name="time" id="qs_time">
                                <span class="input-group-text">seconds</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Score:</label>
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="10" name="score" id="qs_sc">
                                <span class="input-group-text">{{strtolower(env('CURRENCY_NAME'))}}s</span>
                            </div>
                        </div>
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
@push('js')
    <script type="text/javascript" src="{{asset("js/jquery-1.11.2.min.js")}}"></script>

    <script type="text/javascript" src="{{asset("js/jquery.datetimepicker.full.min.js")}}"></script>
    <script>
        $(document).on("click", ".btn-del", function (ev) {
            ev.preventDefault();
            $("#qs-id").val($(this).data('id'));
        });
        $(document).on("click", ".btn-edit", function (ev) {
            ev.preventDefault();
            $("#qs_id").val($(this).data('id'));
            $("#qs_qs").val($(this).data('qs'));
            $("#qs_op").text($(this).data('op'));
            $("#qs_ans").val($(this).data('ans'));
            $("#qs_time").val($(this).data('time'));
            $("#qs_sc").val($(this).data('sc'));
        });
        jQuery(document).ready(function () {
            'use strict';
            jQuery('#begin-date, #result-until').datetimepicker({
                format: 'H:i d/m/Y',
                step: 30,
            });
        });

    </script>
@endpush

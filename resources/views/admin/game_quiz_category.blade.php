@extends('layouts.head')
@push('style')
    <style>
        .fixed-img-height {
            height: 200px !important;
            display: block;
            margin: 0 auto;
            object-fit: none;
            object-position: center;
        }

        .fixed-img-bottom {
            background-color: rgba(0, 0, 0, 0.7);
            padding-top: 5px;
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
@endpush
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
        <div class="col-lg-4 col-md-5 col-sm-12">
            <form class="card px-0" method="post" action="{{route('game_quiz_category_add')}}"
                  enctype="multipart/form-data">
                @csrf
                <div class="card-header bg-blue-lt text-dark">
                    <span class="card-title">Add a Category</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Category name:</label>
                        <input type="text" class="form-control" name="quiz_category_name" placeholder="General"
                               value="{{old('quiz_category_name')}}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Card image:</label>
                        <div class="form-file">
                            <input type="file" name="quiz_card_image" class="form-file-input img-input" id="imagefile">
                            <label class="form-file-label" for="customFile">
                                <span class="form-file-text img-choose">Choose image...</span>
                                <span class="form-file-button">Browse</span>
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description:</label>
                        <textarea class="form-control" name="quiz_category_description" rows="3"
                                  placeholder="Enter a description here...">{!!old('quiz_category_description')!!}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Time limit per quiz:</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="quiz_category_time" placeholder="30"
                                   value="{{old('quiz_category_time')}}">
                            <span class="input-group-text">seconds</span>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Add this category</button>
                </div>
            </form>
            <div class="card card-body">
                <a href="#" class="btn btn-block btn-primary" data-toggle="modal" data-target="#cat-update"
                   data-backdrop="static" data-keyboard="false">Update Quiz Settings</a>
            </div>
        </div>
        <div class="col-lg-8 col-md-7 col-sm-12">
            <div class="alert alert-info">Click on a category to administer questions.</div>
            <div class="row">
                @foreach($data as $c)
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="card">
                            <a href="{{route('game_quiz', ['id' => $c->id])}}" class="d-block bg-gray-lt">
                                <img src="{{$c->image}}" class="fixed-img-height w-100">
                            </a>
                            <div class="btns">
                                <a href="#" class="btn-close" data-id="{{$c->id}}" data-toggle="modal"
                                   data-target="#cat-del" data-backdrop="static" data-keyboard="false">
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
                                <a href="#" class="btn-edit" data-id="{{$c->id}}" data-title="{{$c->title}}"
                                   data-desc="{{$c->description}}" data-time="{{$c->quiz_time}}" data-toggle="modal"
                                   data-target="#cat-edit" data-backdrop="static" data-keyboard="false">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24"
                                         viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                         stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z"/>
                                        <path d="M9 7 h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3"/>
                                        <path d="M9 15h3l8.5 -8.5a1.5 1.5 0 0 0 -3 -3l-8.5 8.5v3"/>
                                        <line x1="16" y1="5" x2="19" y2="8"/>
                                    </svg>
                                </a>
                            </div>
                            <div class="fixed-img-bottom w-100 pl-2 pr-2">
                                <div class="h4 mb-1">{{$c->title}}</div>
                                <div class="h5">{{$c->description}}</div>
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
    <form method="post" action="{{route('game_quiz_category_del')}}" class="modal modal-blur fade" id="cat-del"
          tabindex="-1" role="dialog" aria-hidden="true">
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
    <form method="post" action="{{route('game_quiz_category_edit')}}" class="modal modal-blur fade" id="cat-edit"
          tabindex="-1" role="dialog" aria-hidden="true" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="id" id="cat-edit-id">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content card">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Category name:</label>
                        <input type="text" class="form-control" name="update_category_name" id="cat_name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Card image:</label>
                        <div class="form-file">
                            <input type="file" name="update_card_image" class="form-file-input img-input"
                                   id="imagefile">
                            <label class="form-file-label" for="customFile">
                                <span class="form-file-text img-choose">Choose image...</span>
                                <span class="form-file-button">Browse</span>
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description:</label>
                        <textarea class="form-control" name="update_category_description" rows="3"
                                  id="cat_desc"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Time limit per quiz:</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="update_category_time" id="cat_time">
                            <span class="input-group-text">sec</span>
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

    <form method="post" action="{{route('game_quiz_settings_update')}}" class="modal modal-blur fade" id="cat-update"
          tabindex="-1" role="dialog" aria-hidden="true">
        @csrf
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content card">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Answer time offset:</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="time" value="{{env('QUIZ_TIME_OFFSET')}}">
                            <span class="input-group-text">seconds</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Disqualify for incorrect answer more than:</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="incorrect"
                                   value="{{env('QUIZ_WRONG_LIMIT')}}">
                            <span class="input-group-text">times</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Round cost:</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="cost" value="{{env('QUIZ_ROUND_COST')}}">
                            <span class="input-group-text">{{strtolower(env('CURRENCY_NAME'))}}s</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Skip cost:</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="skip" value="{{env('QUIZ_SKIP_COST')}}">
                            <span class="input-group-text">{{strtolower(env('CURRENCY_NAME'))}}s</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">50/50 cost:</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="fifty" value="{{env('QUIZ_FIFTY_COST')}}">
                            <span class="input-group-text">{{strtolower(env('CURRENCY_NAME'))}}s</span>
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
@endsection
@push('js')
    <script>
        $('.img-input').on('change', function () {
            var fileName = $(this).val().split('\\').pop();
            $(this).closest('.form-file').find('.img-choose').addClass("selected").text(fileName);
        });
        $(document).on("click", ".btn-close", function (ev) {
            ev.preventDefault();
            $("#cat-id").val($(this).data('id'));
        });
        $(document).on("click", ".btn-edit", function (ev) {
            ev.preventDefault();
            $("#cat-edit-id").val($(this).data('id'));
            $("#cat_name").val($(this).data('title'));
            $("#cat_desc").text($(this).data('desc'));
            $("#cat_reward").val($(this).data('reward'));
            $("#cat_cost").val($(this).data('cost'));
            $("#cat_time").val($(this).data('time'));
        });

    </script>
@endpush

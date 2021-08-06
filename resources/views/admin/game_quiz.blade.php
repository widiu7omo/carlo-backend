@extends('layouts.head')
@section('css')
<script type="text/javascript" src="{{asset("js/jquery-1.11.2.min.js"}}"></script>
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
    <div class="col-lg-4 col-md-6 col-sm-12">
        <form class="card px-0" method="post" action="{{route('game_quiz_add')}}">
            @csrf
            <input type="hidden" name="id" value="{{$data['id']}}" />
            <div class="card-header bg-blue-lt text-dark">
                <span class="card-title">{{$data['header_title']}}</span>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Question:</label>
                    <textarea class="form-control" name="quiz_question" rows="2" placeholder="{{$data['placeholder_question']}}">{{old('quiz_question')}}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{$data['function_title']}}</label>
                    <textarea class="form-control" name="quiz_function" rows="4" placeholder="{!!$data['placeholder_function']!!}">{!!old('quiz_function')!!}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">For countries:</label>
                    <input type="text" class="form-control" name="quiz_country" placeholder="US,GB,AU" value="{{old('quiz_country')}}">
                </div>
            </div>
            <div class="card-footer row">
                <div class="col-6">
                    <button type="submit" class="btn-block btn btn-primary">Add this quiz</button>
                </div>
                <div class="col-6">
                    <a href="{{route('game_quiz_category')}}" class="btn btn-block btn-secondary">Back</a>
                </div>
            </div>
        </form>
    </div>
    <div class="col-lg-8 col-md-6 col-sm-12">
        <div class="d-flex mb-3 mx-1">
            Per quiz time: {{$data['time']}} seconds
            <span class="badge ml-auto bg-blue-lt px-3 py-1">Category:
                <span class="badge-addon bg-blue">{{$data['cat']}}</span>
            </span>
        </div>
        <div class="card">
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th class="py-3">ID</th>
                            <th class="py-3">Question</th>
                            <th class="w-1"></th>
                            <th class="w-1"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['q'] as $d)
                        <tr>
                            <td class="text-muted">{{$d->id}}</td>
                            <td>{{$d->question}}</td>
                            <td>
                                <a href="#" class="btn-edit" data-id="{{$d->id}}" data-qs="{{$d->question}}" data-func="{!!$d->category == 1 ? str_replace('; ','; &#10;', $d->functions) : str_replace('||','&#10;', $d->functions).'&#10;$answer = '.$d->answer!!}" data-ctry="{{$d->country == 'all' ? '' : $d->country}}" data-toggle="modal" data-target="#qs-edit" data-backdrop="static" data-keyboard="false">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" />
                                        <path d="M9 7 h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3" />
                                        <path d="M9 15h3l8.5 -8.5a1.5 1.5 0 0 0 -3 -3l-8.5 8.5v3" />
                                        <line x1="16" y1="5" x2="19" y2="8" /></svg>
                                </a>
                            </td>
                            <td>
                                <a href="#" class="btn-del" data-id="{{$d->id}}" data-toggle="modal" data-target="#qs-del" data-backdrop="static" data-keyboard="false">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" />
                                        <line x1="4" y1="7" x2="20" y2="7" />
                                        <line x1="10" y1="11" x2="10" y2="17" />
                                        <line x1="14" y1="11" x2="14" y2="17" />
                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer d-flex align-items-center">
                <p class="m-0 text-muted">Showing <span>{{$data['q']->firstItem()}}</span> to <span>{{$data['q']->lastItem()}}</span> of <span>{{$data['q']->total()}}</span> entries</p>
                <ul class="pagination m-0 ml-auto">
                    {{ $data['q']->appends(request()->except('page'))->links() }}
                </ul>
            </div>
        </div>
    </div>
</div>
<form method="post" action="{{route('game_quiz_del')}}" class="modal modal-blur fade" id="qs-del" tabindex="-1" role="dialog" aria-hidden="true">
    @csrf
    <input type="hidden" name="id" id="qs-id">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-title">Are you sure?</div>
                <div>You are about to remove this this questions from your database.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary mr-auto" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger">Yes, delete it</button>
            </div>
        </div>
    </div>
</form>
<form method="post" action="{{route('game_quiz_edit')}}" class="modal modal-blur fade" id="qs-edit" tabindex="-1" role="dialog" aria-hidden="true">
    @csrf
    <input type="hidden" name="id" id="qs_id">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content card">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Question:</label>
                    <textarea class="form-control" name="update_question" rows="2" id="qs_qs"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{$data['function_title']}}</label>
                    <textarea class="form-control" name="update_function" rows="4" id="qs_func"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">For countries:</label>
                    <input type="text" class="form-control" name="update_country" id="qs_ctry">
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
@section('javascript')
<script>
    $(document).on("click", ".btn-del", function (ev) {
        ev.preventDefault();
        $("#qs-id").val($(this).data('id'));
    });
    $(document).on("click", ".btn-edit", function (ev) {
        ev.preventDefault();
        $("#qs_id").val($(this).data('id'));
        $("#qs_qs").text($(this).data('qs'));
        $("#qs_func").text($(this).data('func'));
        $("#qs_ctry").val($(this).data('ctry'));
    });

</script>
@endsection

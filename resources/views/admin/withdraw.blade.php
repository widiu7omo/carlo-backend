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
            {{$errors->first()}}
        </div>
    @endif
    <div class="row">
        @foreach($wd['pending'] as $w)
            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="card">
                    <div class="d-flex pb-0">
                        <button data-id="{{$w->id}}" type="button" class="close discard ml-auto mr-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                 viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z"/>
                                <line x1="18" y1="6" x2="6" y2="18"/>
                                <line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                        </button>
                    </div>
                    <div class="card-body pt-0">
                        <h3 class="card-title mb-0 text-green">{{$w->g_name}}</h3>
                        <h5 class="my-0">{{env('CURRENCY_NAME')}}s in exchange: <span
                                class="text-muted">{{$w->points}}</span></h5>
                        <h4 class="mt-2 mb-4">Send to: <span class="text-azure">{{$w->to_acc}}</span></h4>
                        <div class="card-text d-flex">
                            <a href="{{route('withdraw_proceed', ['id' => $w->id])}}" class="btn btn-primary">Mark
                                proceed</a>
                            <a data-wid="{{$w->id}}" class="btn checker btn-white ml-auto">Fraud check</a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        <div class="col-lg-12 d-flex justify-content-center">
            <ul class="pagination">
                {{ $wd['pending']->appends(request()->except('p'))->links() }}
            </ul>
        </div>
    </div>


    @if(!$wd['completed']->isEmpty())
        <div class="row">
            <div class="box">
                <div class="card">
                    <div class="card-header pb-1 pt-2 bg-gray-lt">
                        <h4 class="text-dark">Withdrawal history</h4>
                    </div>
                    <div class=" table-responsive">
                        <table class="table table-vcenter card-table table-striped">
                            <thead>
                            <tr>
                                <th>Method</th>
                                <th>To</th>
                                <th>User ID</th>
                                <th>{{env('CURRENCY_NAME')}}s</th>
                                <th style="width:180px">Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($wd['completed'] as $c)
                                <tr>
                                    <td class="text-muted">{{$c->g_name}}</td>
                                    <td class="text-muted">{{$c->to_acc}}</td>
                                    <td class="text-dark"><a href="{{route('userinfo', ['userid' => $c->userid])}}"
                                                             class="text-reset">{{$c->userid}}</a></td>
                                    <td class="text-muted">{{$c->points}}</td>
                                    <td class="text-muted text-nowrap">{{$c->created_at}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer d-flex align-items-center pt-1 pb-2">
                        <p class="m-0 text-muted">Showing <span>{{$wd['completed']->firstItem()}}</span> to
                            <span>{{$wd['completed']->lastItem()}}</span> of <span>{{$wd['completed']->total()}}</span>
                            entries</p>
                        <ul class="pagination m-0 ml-auto">
                            {{ $wd['completed']->appends(request()->except('c'))->links() }}
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <form action="{{route('withdraw_discard')}}" method="post" class="modal modal-blur fade" id="modal-confirmation"
          tabindex="-1" role="dialog" aria-hidden="true">
        @csrf
        <input type="hidden" name="id" id="refuse-id">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <label class="mb-3 text-red">If you refuse to pay, requested {{strtolower(env('CURRENCY_NAME'))}}s
                        will be returned to the user's balance.</label>
                    <label class="form-label">Reason for refusing:</label>
                    <textarea class="form-control" id="reasoning" name="reason" rows="3"
                              placeholder="Write a reason if you want to notify the user. You can leave this field blank if you don't want to notify."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white mr-auto" data-dismiss="modal">Back</button>
                    <button type="submit" class="btn btn-primary">Reject Request</button>
                </div>
            </div>
        </div>
    </form>

    <div class="modal modal-blur fade" id="modal-info" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header modal-title">Fraud checker</div>
                <div id="modal-content" class="modal-body">

                </div>
                <div id="mdc" class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-block" data-dismiss="modal">Close this window
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('style')
    <meta name="csrf-token" content="{{csrf_token()}}"/>
@endpush
@push('js')
    <script type="text/javascript" src="{{asset("js/jquery-1.11.2.min.js")}}"></script>
    <script type="text/javascript" src="{{asset("js/bootstrap.bundle.min.js")}}"></script>
    <script>
        $(document).on("click", ".discard", function (ev) {
            ev.preventDefault();
            $("#refuse-id").val($(this).data('id'));
            $("#reasoning").val('');
            $("#modal-confirmation").modal({
                backdrop: 'static',
                keyboard: false
            });
        });

        var wid = '';
        $(document).on("click", ".checker", function (ev) {
            ev.preventDefault();
            $("#modal-info").modal({
                backdrop: 'static',
                keyboard: false
            });
            var new_wid = $(this).data('wid');
            if (wid != new_wid) {
                wid = new_wid;
                var element = document.getElementById("modal-content");
                element.innerHTML = '<div class="text-center my-4">Please wait while we are checking...</div>'
                $.ajax({
                    type: 'POST',
                    url: '{{route("withdraw_info")}}?wid=' + wid,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        var to_print = '';
                        if (data.hasOwnProperty('wd')) {
                            to_print += decoration(1, 'Withdraw requested', 'YES');
                        } else {
                            to_print += decoration(0, 'Withdraw requested', 'NO');
                        }
                        if (data.hasOwnProperty('user')) {
                            to_print += decoration(1, 'User exist', 'YES');
                        } else {
                            to_print += decoration(0, 'User exist', 'NO');
                        }
                        if (data.hasOwnProperty('banned')) {
                            to_print += decoration(0, 'User banned', 'YES');
                        } else {
                            to_print += decoration(1, 'User banned', 'NO');
                        }
                        if (data.hasOwnProperty('balance')) {
                            to_print += decoration(1, 'Request validation', 'YES');
                        } else {
                            to_print += decoration(0, 'Request validation', 'either balance is negative or requested amount exceeds total pending amount.</span></div>');
                        }
                        if (data.history == 1) {
                            to_print += decoration(1, 'History lookup', 'OK');
                            ;
                        } else {
                            to_print += decoration(0, 'History lookup', data.history);
                            ;
                        }
                        if (data.country == 1) {
                            to_print += decoration(1, 'Country lookup', 'PASSED');
                        } else {
                            to_print += decoration(2, 'Country lookup', data.country);
                        }
                        element.innerHTML = to_print;
                    },
                    error: function (request, status, error) {
                        $("#mdc").removeClass('d-none');
                        alert(request.responseText);
                    }
                });
            }
        });

        function decoration(t, t1, t2) {
            if (t == 1) {
                return '<div class="mb-1 p-2 border border-success" style="color:#167316 !important"><span class="font-weight-bold">' + t1 + '</span>: <span class="ml-2">' + t2 + '</span></div>';
            } else if (t == 2) {
                return '<div class="mb-1 p-2 border border-primary text-primary"><span class="font-weight-bold">' + t1 + '</span>: <span class="ml-2">' + t2 + '</span></div>';
            } else {
                return '<div class="mb-1 p-2 border border-danger text-danger"><span class="font-weight-bold">' + t1 + '</span>: <span class="ml-2">' + t2 + '</span></div>';
            }
        }

    </script>
@endpush

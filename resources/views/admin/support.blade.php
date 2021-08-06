@extends('layouts.head')
@section('css')
<meta name="csrf-token" content="{{csrf_token()}}" />
<script type="text/javascript" src="/public/js/jquery-1.11.2.min.js"></script>
<style>
    .cb {
        height: 500px !important;
        overflow-y: auto;
    }

    .cb-child {
        height: 480px !important;
    }

    .cb-btn {
        width: 130px !important
    }

    .notif {
        height: 25px;
        width: 25px;
        line-height: 20px;
        font-size: 14px
    }

    .d-flex h6 {
        margin-right: 70px
    }

</style>
@endsection
@section('content')
<div class="row h-100">
    <div class="col-lg-4 col-md-5 col-sm-12">
        @foreach($data as $d)
        <div class="card card-sm mb-2">
            <div class="card-body row">
                <div onclick="getMessage('{{$d->name}}','{{$d->userid}}');" class="cursor-pointer col-10 d-flex align-items-center">
                    <div class="ml-2 lh-sm w-100">
                        <div class="strong">{{$d->name}}</div>
                        <div class="text-muted">{{$d->email}}</div>
                    </div>
                    @if($d->new != '0')
                    <span id="{{$d->userid}}" class="badge notif badge-pill bg-red ml-2 mr-3">{{$d->new}}</span>
                    @endif
                </div>
                <a data-id="{{$d->userid}}" class="btn-del cursor-pointer col-2 bg-blue-lt justify-content-center align-items-center d-flex" data-toggle="modal" data-target="#qs-del" data-backdrop="static" data-keyboard="false">
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
        @endforeach
        <div class="col-lg-12 mt-3 d-flex justify-content-center">
            <ul class="pagination">
                {{ $data->appends(request()->except('p'))->links() }}
            </ul>
        </div>
    </div>
    <div class="col-lg-8 col-md-7 col-sm-12">
        <div class="card">
            <div id="userinfo" class="card-header">Chat box</div>
            <div id="msg-parent" class="card-body cb align-items-end">
                <div id='msg' class="w-100">
                    <div class="cb-child d-flex align-items-center justify-content-center">
                        Click on a conversation from left to see the messages here.
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex">
                <input type="hidden" id="userid" />
                <input id="msg-input" type="text" class="form-control mr-3" placeholder="Write here...">
                <a id='sbtn' class="ml-auto btn btn-dark cb-btn text-white" onclick="sendMessage();">Send</a>
            </div>
        </div>
    </div>
</div>
<form method="post" action="{{route('support_del_full')}}" class="modal modal-blur fade" id="qs-del" tabindex="-1" role="dialog" aria-hidden="true">
    @csrf
    <input type="hidden" name="id" id="qs-id">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-title">Are you sure?</div>
                <div>You are about to remove this whole conversation.</div>
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
    var submit = false;
    $(document).on("click", ".btn-del", function (ev) {
        ev.preventDefault();
        $("#qs-id").val($(this).data('id'));
    });
    var element = document.getElementById("msg");
    var scroller = document.getElementById("msg-parent");

    function getMessage(name, uid) {
        element.innerHTML = '<div class="cb-child d-flex align-items-center justify-content-center">Please wait...</div>';
        $.ajax({
            type: 'POST',
            url: '{{route("support_chat")}}?uid=' + uid,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (data) {
                var output = '';
                var length = data.msgs.length;
                for (var i = 0; i < length; i++) {
                    var object = data.msgs[i];
                    if (object.is_staff == 1) {
                        output += '<div class="d-flex ml-6"><div class="ml-auto rounded bg-dark text-white p-2 my-3 ml-6 mr-0"><div class="d-flex"><h6>' + object.updated_at + '</h6><a class="cursor-pointer ml-auto mr-2" onclick="delMessage(' + object.id + ');">X</a></div>' + object.message + '</div></div>';
                    } else {
                        output += '<div class="d-flex mr-6"><div class="mr-auto rounded bg-blue-lt p-2 my-3 ml-0"><div class="d-flex"><h6>' + object.updated_at + '</h6><a class="cursor-pointer ml-auto mr-2" onclick="delMessage(' + object.id + ');">X</a></div>' + object.message + '</div></div>';
                    }
                }
                element.innerHTML = output;
                $("#userid").val(uid);
                $("#userinfo").html(name + '<a onclick="markMessage();" class="ml-auto btn btn-sm btn-secondary text-white">Mark as read</a>');
                scroller.scrollTop = scroller.scrollHeight;
                submit = false;
            },
            error: function (request, status, error) {
                submit = false;
                alert(request.responseText);
            }
        });
    }

    function markMessage() {
        if (submit == false) {
            submit = true;
            var uid = $("#userid").val();
            $.ajax({
                type: 'POST',
                url: '{{route("support_mark")}}?uid=' + uid,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {
                    $("#" + uid).addClass('d-none');
                    submit = false;
                },
                error: function (request, status, error) {
                    submit = false;
                    alert(request.responseText);
                }
            });
        }
    }

    function sendMessage() {
        var userid = $("#userid").val();
        var mg = $("#msg-input").val();
        if (userid == null || userid == '') return;
        if (mg == null || mg == '') return;
        if (submit == false) {
            submit = true;
            $("#sbtn").text('Sending...');
            $.ajax({
                type: 'POST',
                url: '{{route("support_send")}}',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                data: JSON.stringify({
                    uid: userid,
                    msg: mg
                }),
                success: function (dta) {
                    var output = '';
                    var length = dta.msgs.length;
                    for (var i = 0; i < length; i++) {
                        var object = dta.msgs[i];
                        if (object.is_staff == 1) {
                            output += '<div class="d-flex ml-6"><div class="ml-auto rounded bg-dark text-white p-2 my-3 ml-6 mr-0"><div class="d-flex"><h6>' + object.updated_at + '</h6><a class="cursor-pointer ml-auto mr-2" onclick="delMessage(' + object.id + ');">X</a></div>' + object.message + '</div></div>';
                        } else {
                            output += '<div class="d-flex mr-6"><div class="mr-auto rounded bg-blue-lt p-2 my-3 ml-0"><div class="d-flex"><h6>' + object.updated_at + '</h6><a class="cursor-pointer ml-auto mr-2" onclick="delMessage(' + object.id + ');">X</a></div>' + object.message + '</div></div>';
                        }
                    }
                    element.innerHTML = output;
                    $("#msg-input").val('')
                    $("#" + userid).addClass('d-none');
                    $("#sbtn").text('Send');
                    scroller.scrollTop = scroller.scrollHeight;
                    submit = false;
                },
                error: function (request, status, error) {
                    submit = false;
                    alert(request.responseText);
                }
            });
        }
    }

    function delMessage(id) {
        if (id == null || id == '') return;
        if (submit == false) {
            submit = true;
            $.ajax({
                type: 'POST',
                url: '{{route("support_del_single")}}?id=' + id,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (dta) {
                    var output = '';
                    var length = dta.msgs.length;
                    for (var i = 0; i < length; i++) {
                        var object = dta.msgs[i];
                        if (object.is_staff == 1) {
                            output += '<div class="d-flex ml-6"><div class="ml-auto rounded bg-dark text-white p-2 my-3 ml-6 mr-0"><div class="d-flex"><h6>' + object.updated_at + '</h6><a class="cursor-pointer ml-auto mr-2" onclick="delMessage(' + object.id + ');">X</a></div>' + object.message + '</div></div>';
                        } else {
                            output += '<div class="d-flex mr-6"><div class="mr-auto rounded bg-blue-lt p-2 my-3 ml-0"><div class="d-flex"><h6>' + object.updated_at + '</h6><a class="cursor-pointer ml-auto mr-2" onclick="delMessage(' + object.id + ');">X</a></div>' + object.message + '</div></div>';
                        }
                    }
                    element.innerHTML = output;
                    scroller.scrollTop = scroller.scrollHeight;
                    submit = false;
                },
                error: function (request, status, error) {
                    submit = false;
                    alert(request.responseText);
                }
            });
        }
    }

</script>
@endsection

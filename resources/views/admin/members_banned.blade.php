@extends('layouts.head')
@section('css')
<link href="/public/css/tabler-flags.min.css" rel="stylesheet" />
<script type="text/javascript" src="/public/js/jquery-1.11.2.min.js"></script>
@endsection
@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-auto">
            <h2 class="page-title">Banned Users</h2>
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
    {{$errors->first()}}
</div>
@endif

<div class="row">
    <div class="box">
        <div class="card">
            <div class="table-responsive">
                <table class="table table-vcenter table-mobile-sm card-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Reason</th>
                            <th>Date / Time</th>
                            <th class="w-1"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($users->isEmpty())
                        <tr>
                            <td colspan="4" class="text-center pb-4 pt-4">
                                <a class="btn btn-dark" href="{{ route('members') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z"></path>
                                        <circle cx="8.5" cy="7" r="4"></circle>
                                        <path d="M2 21v-2a4 4 0 0 1 4 -4h5a4 4 0 0 1 4 4v2"></path>
                                        <line x1="16" y1="11" x2="22" y2="11"></line>
                                        <line x1="19" y1="8" x2="19" y2="14"></line>
                                    </svg>
                                    Keep banning bad users
                                </a>
                            </td>
                        </tr>
                        @else
                        @foreach ($users as $u)
                        <tr>
                            <td data-label="Name">
                                <div class="d-flex lh-sm py-1 align-items-center">
                                    @if($u->avatar == null)
                                    <span class="avatar mr-2 avatar-md flag-country-{{$u->country}}"></span>
                                    @else
                                    <span class="avatar avatar-md mr-2" style="background-image: url({{$u->avatar}})"></span>
                                    @endif
                                    <div class="flex-fill">
                                        <div class="strong">{{$u->name}}</div>
                                        <div class="text-muted text-h5"><a href="#" class="text-reset">{{$u->email}}</a></div>
                                    </div>
                                </div>
                            </td>
                            <td data-label="Reason">
                                <div class="text-muted text-h5">{{$u->reason}}</div>
                            </td>
                            <td class="text-muted text-nowrap" data-label="Date / Time">
                                {{\Carbon\Carbon::parse($u->created)->diffForHumans()}}
                            </td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <a class="btn btn-white modalbtn" data-keyboard="false" data-backdrop="static" data-toggle="modal" data-target="#edit-reason" data-userid="{{$u->userid}}" data-reason="{{$u->reason}}">Edit</a>
                                    <a class="btn btn-dark" href="{{ route('unban') }}?uid={{$u->userid}}">Unban</a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        <div class="float-right text-nowrap flex-nowrap">
            {{ $users->appends(request()->except('page'))->links() }}
        </div>
    </div>

    <form action="" method="POST" class="modal modal-blur fade" id="edit-reason" tabindex="-1" role="dialog" aria-hidden="true">
        @csrf
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update banning reason:</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" />
                            <line x1="18" y1="6" x2="6" y2="18" />
                            <line x1="6" y1="6" x2="18" y2="18" /></svg>
                    </button>
                </div>
                <div class="modal-body">
                    <textarea id="reasoning" name="reason" class="form-control" data-toggle="autosize"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white mr-auto" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('javascript')
<script type="text/javascript">
    $(document).on("click", ".modalbtn", function (ev) {
        ev.preventDefault();
        var userid = $(this).data('userid');
        var reason = $(this).data('reason');
        $("#edit-reason").attr('action', '{{ route("edit_ban") }}?uid=' + userid);
        $(".modal-body #reasoning").text(reason);
    });

</script>
@endsection

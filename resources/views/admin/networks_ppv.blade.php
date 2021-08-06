@extends('layouts.head')
@section('css')
<style>
    .small-col {
        width: 180px
    }

</style>
<script type="text/javascript" src="/public/js/jquery-1.11.2.min.js"></script>
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
    <form class="card px-0" method="post" action="{{route('networks_ppv_add')}}">
        @csrf
        <div class="card-header">
            <span class="card-title">Add a page for <i>Pay Per View</i></span>
        </div>
        <div class="card-body row">
            <div class="mb-3 col-md-6 col-sm-12">
                <label class="form-label">Enter a title:</label>
                <input type="text" class="form-control" name="ppv_title" placeholder="My awesome website" value="{{old('ppv_title')}}">
            </div>
            <div class="mb-3 col-md-6 col-sm-12">
                <label class="form-label">Enter URL of the landing page:</label>
                <input type="text" class="form-control" name="ppv_url" placeholder="https://mintservice.ltd" value="{{old('ppv_url')}}">
            </div>
            <div class="mb-3 col-md-4 col-sm-12">
                <label class="form-label">Time to stay on that page:</label>
                <div class="input-group">
                    <input type="text" class="form-control" name="ppv_time" placeholder="30" value="{{old('ppv_time')}}">
                    <span class="input-group-text">seconds</span>
                </div>
            </div>
            <div class="mb-3 col-md-4 col-sm-12">
                <label class="form-label">Amount will be rewarded:</label>
                <div class="input-group">
                    <input type="text" class="form-control" name="reward_amount" placeholder="100" value="{{old('reward_amount')}}">
                    <span class="input-group-text">seconds</span>
                </div>
            </div>
            <div class="mb-3 col-md-4 col-sm-12">
                <label class="form-label">Country ISO <small>(blank for all)</small>:</label>
                <input type="text" class="form-control" name="country_iso" placeholder="US,UK,AU" value="{{old('country_iso')}}">
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Add this page</button>
        </div>
    </form>
</div>
<div class="row">
    <div class="card px-0">
        <div class="card-header pb-1 pt-2 bg-gray-lt">
            <h4 class="text-dark">Existing PPV offers</h4>
        </div>
        <div class=" table-responsive">
            <table class="table table-vcenter card-table table-striped">
                <thead>
                    <tr>
                        <th class="small-col">Offer ID</th>
                        <th>Title</th>
                        <th class="small-col text-center">Seconds</th>
                        <th class="small-col text-center">Amount</th>
                        <th class="small-col">Date</th>
                        <th class="w-1"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $d)
                    <tr>
                        <td class="text-muted">{{$d->id}}</td>
                        <td class="text-muted strong"><a href="{{$d->url}}" target="_blank">{{$d->title}}</a></td>
                        <td class="text-dark text-center">{{$d->seconds}}</td>
                        <td class="text-muted text-center">{{$d->points}}</td>
                        <td class="text-muted text-nowrap">{{$d->created_at}}</td>
                        <td>
                            <a href="#" class="ppv_close" data-id="{{$d->id}}" data-toggle="modal" data-target="#ppv-del" data-backdrop="static" data-keyboard="false">
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
        <div class="card-footer d-flex align-items-center pt-1 pb-2">
            <p class="m-0 text-muted">Showing <span>{{$data->firstItem()}}</span> to <span>{{$data->lastItem()}}</span> of <span>{{$data->total()}}</span> entries</p>
            <ul class="pagination m-0 ml-auto">
                {{ $data->appends(request()->except('c'))->links() }}
            </ul>
        </div>
    </div>
</div>
<form method="post" action="{{route('networks_ppv_del')}}" class="modal modal-blur fade" id="ppv-del" tabindex="-1" role="dialog" aria-hidden="true">
    @csrf
    <input type="hidden" name="id" id="ppv-id">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-title">Are you sure?</div>
                <div>You are about to remove this PPV offer from your database.</div>
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
    $(document).on("click", ".ppv_close", function (ev) {
        ev.preventDefault();
        $("#ppv-id").val($(this).data('id'));
    });

</script>
@endsection

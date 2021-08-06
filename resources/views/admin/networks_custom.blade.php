@extends('layouts.head')
@section('css')
<style>
    .linebreak {
        white-space: normal !important;
    }

    .linebreak span {
        font-size: 11px !important;
        color: #888;
        display: block
    }

    .space-between {
        justify-content: space-between;
    }

    .small-btn {
        font-size: 13px;
        padding: 4px 15px !important
    }

    .icon-width {
        max-width: 40px;
        min-width: 36px;
    }

</style>
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
    {!!$errors->first()!!}
</div>
@endif
<form class="row" method="post" action="{{route('networks_custom_postback')}}">
    @csrf
    <div class="col-12">
        <div class="card">
            <div class="card-body row">
                <div class="col-9">
                    <label class="form-label text-truncate">Enter any alphanumeric characters to get your <span class="text-danger">Custom CPA</span> postback:</label>
                    <input type="text" class="form-control" name="custom_token" placeholder="jgsdja76a8dajsd" value="{{$data['pb']}}">
                </div>
                <div class="col-3">
                    <label class="form-label">.</label>
                    <button type="submiy" class="btn btn-block btn-primary">Update</button>
                </div>
                @if($data['pb'] != null)
                <div class="col-12 mt-3 input-group">
                    <span class="input-group-text text-blue">Postback URL:</span>
                    <input type="text" class="form-control" value="{{env('APP_URL')}}/api/cpb?tok={{$data['pb']}}&uid={user_id}&offerid={offer_id}&ip={ip}" readonly="">
                </div>
                @endif
            </div>
        </div>
    </div>
</form>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header space-between">
                <h3 class="card-title">Custom Offers</h3>
                <a href="{{route('networks_custom_new')}}" class="btn btn-primary small-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z"></path>
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Add Offer
                </a>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap datatable">
                    <thead>
                        <tr>
                            <th>Icon</th>
                            <th style="min-width:300px">Title / Desc</th>
                            <th class="text-center">Offer ID</th>
                            <th class="text-center">Type</th>
                            <th class="text-center">Country</th>
                            <th class="text-center">{{env('CURRENCY_NAME')}}s</th>
                            <th class="text-center">Completed / Max</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['offers'] as $d)
                        <tr>
                            <td><img src="{{$d->image}}" class="icon-width" alt="Icon"></td>
                            <td class="linebreak"><a href="@if($d->type == 2) {{str_replace('?','?offerid='.$d->offer_id.'&', $d->url)}} @else {{$d->url}} @endif">{{$d->title}}</a> <span>{{$d->description}}</span></td>
                            <td class="text-center"><span class="text-muted">{{$d->offer_id}}<span></td>
                            <td class="text-center">@if ($d->type == 1) CPI @else CPA @endif</td>
                            <td class="text-center">{{$d->country}}</td>
                            <td class="text-center">{{$d->points}}</td>
                            <td class="text-center"><span class="text-success">{{$d->completed}}</span> / <span class="text-danger">{{$d->max}}</span></td>
                            <td class="text-center"><a href="{{route('networks_custom_edit', ['id' => $d->id])}}">Edit</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer d-flex align-items-center">
                <p class="m-0 text-muted">Showing <span>{{$data['offers']->firstItem()}}</span> to <span>{{$data['offers']->lastItem()}}</span> of <span>{{$data['offers']->total()}}</span> entries</p>
                <ul class="pagination m-0 ml-auto">
                    {{ $data['offers']->appends(request()->except('page'))->links() }}
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection
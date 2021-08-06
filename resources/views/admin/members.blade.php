@extends('layouts.head')
@section('css')
<link href="/public/css/tabler-flags.min.css" rel="stylesheet" />
<style>
    .hover {
        padding: 10px 0px;
        background: #ffffff;
    }

    .hover:hover {
        background: #dddddd;
        transition: all 1s ease;
        -webkit-transition: all 1s ease;
        cursor: pointer;
    }

    .space-between {
        justify-content: space-between;
    }

    .small-btn {
        font-size: 13px;
        padding: 4px 10px !important
    }

    .icon-padding {
        padding: 50px 0px
    }

    .text-avatar {
        font-size: 13px !important
    }
    .line-height-none{
        line-height:11px !important;
    }
</style>
@endsection
@section('content')
@if ($errors->any())
<div class="alert alert-warning alert-dismissible">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    {{$errors->first()}}
</div>
@endif
<div class="row">
    <div class="col-md-12">
        <div class="card">
            @if($data['users']->isEmpty())
            <div class="container-xl d-flex flex-column justify-content-center icon-padding">
                <div class="empty">
                    <div class="empty-icon">
                        <img src="/public/img/user_search_icon.png" height="128" class="mb-4" alt="">
                    </div>
                    <p class="empty-title h3">No results found</p>
                    <p class="empty-subtitle text-muted">
                        Try adjusting your search to find what you're looking for.
                    </p>
                </div>
            </div>
            @else
            <div class="card-header space-between">
                <h3 class="card-title">{{$data['title']}}</h3>
                {!!$data['btn']!!}
            </div>
            <div class="card-body">
                <div class="row mb-n6">
                    {!!$data['htmlcode']!!}
                </div>
            </div>
            @endif
            <div class="card-footer d-flex align-items-center">
                <ul class="pagination m-0 ml-auto">
                    {{ $data['users']->appends(request()->except('page'))->links() }}
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
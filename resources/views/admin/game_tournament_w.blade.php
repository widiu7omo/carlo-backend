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
<div class="row justify-content-center">
    <form class="card col-md-5 col-sm-8 col-12 px-0" method="post" action="{{route('game_tour_winner_update')}}">
        @csrf
        <input type="hidden" name="s" value="{{$data['c']}}" />
        <div class="card-header bg-dark-lt">Add winning amount by percentage:</div>
        <div class="card-body">
            @foreach($data['d'] as $d)
            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text">{{$d['name']}}:</span>
                    <input type="text" class="form-control" name="{{$d['param']}}" value="{{old($d['param'], $d['pct'])}}">
                    <span class="input-group-text">%</span>
                </div>
            </div>
            @endforeach
        </div>
        <div class="card-footer d-flex">
            <button type="submit" class="btn btn-secondary">Update winners data</button>
            <a href="{{route('game_tour')}}" class="btn btn-outline-secondary ml-auto">Cancel</a>
        </div>
    </form>
</div>
@endsection

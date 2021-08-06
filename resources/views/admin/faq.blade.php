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
    <div class="card">
        <div class="card-header">FAQ Management</div>
        <div class="card-body">
            <form class="p-0 m-0" action="{{route('faq_add')}}" method="post">
                @csrf
                <div class="mb-2">
                    <input type="text" class="form-control" name="faq_question" placeholder="Enter a question" value="{{old('faq_question')}}">
                </div>
                <div class="mb-3 d-flex">
                    <input type="text" class="form-control mr-2" name="faq_answer" placeholder="Write an answer of above question" value="{{old('faq_answer')}}">
                    <button type="submit" class="btn ml-auto btn-primary text-nowrap">Add FAQ</button>
                </div>
            </form>
            @foreach($faq as $f)
            <div class="bg-gray-lt d-flex rounded p-2 mt-4 text-dark">
                <div>{{$f->question}}</div>
                <a class="ml-auto" href="{{route('faq_del', ['id' => $f->id])}}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" />
                        <line x1="4" y1="7" x2="20" y2="7" />
                        <line x1="10" y1="11" x2="10" y2="17" />
                        <line x1="14" y1="11" x2="14" y2="17" />
                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                </a>
            </div>
            <div class="p-2">{{$f->answer}}</div>
            @endforeach
            <div class="col-lg-12 d-flex justify-content-center mt-3">
                <ul class="pagination">
                    {{ $faq->appends(request()->except('page'))->links() }}
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

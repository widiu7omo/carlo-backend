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
    {!!$errors->first()!!}
</div>
@endif
<form method="post" class="row" action="{{route('global_msg_update')}}">
    @csrf
    <div class="col-12">
        <div class="card">
            <div class="card-header">Global message</div>
            <div class="card-body row pt-3 pb-0">
                <div class="col-md-4 col-12 mb-3">
                    <input type="text" class="form-control" name="title" value="{{old('title',$data['title'])}}" placeholder="Enter a title...">
                </div>
                <div class="col-md-6 col-12 mb-3">
                    <input type="text" class="form-control" name="desc" value="{{old('desc',$data['desc'])}}" placeholder="Enter yout message...">
                </div>
                <div class="col-md-2 col-12 mb-3">
                    <button type="submit" class="btn btn-block btn-primary">Update</button>
                </div>
            </div>
        </div>
    </div>
</form>
<form method="post" class="row" action="{{route('local_msg_send')}}">
    @csrf
    <div class="col-12">
        <div class="card">
            <div class="card-header h3">Send local message</div>
            <div class="card-body row pt-3 pb-0">
                <div class="col-md-6 col-sm-12 mb-3">
                    <label class="form-label">Title:</label>
                    <input type="text" class="form-control" name="title" value="{{old('title')}}" placeholder="Enter a message headline...">
                </div>
                <div class="col-md-6 col-sm-12 mb-3">
                    <label class="form-label">Send to <small>(Email or User ID)</small>:</label>
                    <input type="text" class="form-control" name="to" value="{{old('to')}}" placeholder="someone@email.tld">
                </div>
                <div class="mb-3">
                    <label class="form-label">Message body</label>
                    <textarea class="form-control" name="message_body" rows="3" placeholder="Enter some message here...">{{old('message_body')}}</textarea>
                </div>
                <div class="col-md-2 col-12 mb-3">
                    <button type="submit" class="btn btn-block btn-primary">Send local message</button>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">Pending messages</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap datatable">
                        <thead>
                            <tr>
                                <th class="w-6">ID</th>
                                <th>User ID</th>
                                <th>Message title</th>
                                <th>Pending since</th>
                                <th class="w-1"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['pending'] as $h)
                            <tr>
                                <td><span class="text-muted">{{$h->id}}<span></td>
                                <td>{{$h->userid}}</td>
                                <td>{{$h->title}}</td>
                                <td>{{$h->date}}</td>
                                <td><a href="{{route('local_msg_del', ['id' => $h->id])}}" class="btn btn-sm btn-danger">Detete</a></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer d-flex align-items-center">
                    <p class="m-0 text-muted">Showing <span>{{$data['pending']->firstItem()}}</span> to <span>{{$data['pending']->lastItem()}}</span> of <span>{{$data['pending']->total()}}</span> entries</p>
                    <ul class="pagination m-0 ml-auto">
                        {{ $data['pending']->appends(request()->except('page'))->links() }}
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

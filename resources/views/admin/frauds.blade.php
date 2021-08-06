@extends('layouts.head')
@push('style')
    <link href="{{asset("css/selectize.css")}}" rel="stylesheet"/>
    <style>
        .btn-close {
            position: absolute;
            top: 0;
            right: 0;
            background-color: rgba(0, 0, 0, 0.5);
            color: #ffffff;
            padding: 0px 5px;
        }

    </style>
@endpush
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
    <div class="row">
        <div class="col-md-6">
            <form class="card" method="post" action="{{route('frauds_update')}}">
                @csrf
                <div class="card-header bg-gray-lt pt-3 pb-2">
                    <h4 class="text-dark">Fraud Prevention</h4>
                </div>
                <div class="card-body">
                    <label class="form-selectgroup-item flex-fill">
                        <input type="checkbox" name="single_account" value="1" class="form-selectgroup-input"
                               @if(env('SINGLE_ACCOUNT')==1) checked @endif>
                        <div class="form-selectgroup-label d-flex align-items-center pl-3 pr-3 pt-1 pb-1 mb-1">
                            <div class="mr-3">
                                <span class="form-selectgroup-check"></span>
                            </div>
                            <div class="form-selectgroup-label-content d-flex align-items-center">
                                <span class="avatar rounded mr-3"
                                      style="background-image: url({{asset("img/single_account.png")}})"></span>
                                <div class="lh-sm">
                                    <div class="strong text-left">Single account per device</div>
                                    <div class="h5 text-muted text-left">Don't let users open more than 1 account from a
                                        device.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </label>
                    <label class="form-selectgroup-item flex-fill">
                        <input type="checkbox" name="vpn_block" value="1" class="form-selectgroup-input"
                               @if(env('VPN_BLOCK')==1) checked @endif>
                        <div class="form-selectgroup-label d-flex align-items-center pl-3 pr-3 pt-1 pb-1 p-3 mb-1">
                            <div class="mr-3">
                                <span class="form-selectgroup-check"></span>
                            </div>
                            <div class="form-selectgroup-label-content d-flex align-items-center">
                                <span class="avatar rounded mr-3"
                                      style="background-image: url({{asset("img/block_vpn.png")}})"></span>
                                <div class="lh-sm">
                                    <div class="strong text-left">Block VPN access</div>
                                    <div class="h5 text-muted text-left">Don't let the user open offers by using VPN.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </label>
                    <label class="form-selectgroup-item flex-fill">
                        <input type="checkbox" name="vpn_monitor" value="0" class="form-selectgroup-input"
                               @if(env('VPN_MONITOR')==1) checked @endif>
                        <div class="form-selectgroup-label d-flex align-items-center pl-3 pr-3 pt-1 pb-1 p-3 mb-1">
                            <div class="mr-3">
                                <span class="form-selectgroup-check"></span>
                            </div>
                            <div class="form-selectgroup-label-content d-flex align-items-center">
                                <span class="avatar rounded mr-3"
                                      style="background-image: url({{asset("img/monitor_vpn.png")}})"></span>
                                <div class="lh-sm">
                                    <div class="strong text-left">Monitor VPN access</div>
                                    <div class="h5 text-muted text-left">Silently detect how many times users attempted
                                        to use VPN.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </label>
                    <label class="form-selectgroup-item flex-fill">
                        <input type="checkbox" name="root_block" value="1" class="form-selectgroup-input"
                               @if(env('ROOT_BLOCK')==1) checked @endif>
                        <div class="form-selectgroup-label d-flex align-items-center pl-3 pr-3 pt-1 pb-1 p-3 mb-1">
                            <div class="mr-3">
                                <span class="form-selectgroup-check"></span>
                            </div>
                            <div class="form-selectgroup-label-content d-flex align-items-center">
                                <span class="avatar rounded mr-3"
                                      style="background-image: url({{asset("img/block_rooted.png")}})"></span>
                                <div class="lh-sm">
                                    <div class="strong text-left">Block rooted device</div>
                                    <div class="h5 text-muted text-left">App will not work on rooted device if this
                                        option gets activated.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </label>
                    <label class="form-selectgroup-item flex-fill">
                        <input type="checkbox" name="auto_ban_multi" value="1" class="form-selectgroup-input"
                               @if(env('AUTO_BAN_MULTI')==1) checked @endif>
                        <div class="form-selectgroup-label d-flex align-items-center pl-3 pr-3 pt-1 pb-1 p-3 mb-1">
                            <div class="mr-3">
                                <span class="form-selectgroup-check"></span>
                            </div>
                            <div class="form-selectgroup-label-content d-flex align-items-center">
                            <span class="avatar rounded mr-3" style="background-image: url({{asset("img/auto_ban_multi.png")}})"></span>
                            <div class="lh-sm">
                                <div class="strong text-left">Auto ban multiple accounts</div>
                                <div class="h5 text-muted text-left">Auto ban who attempts to create multiple accounts.</div>
                            </div>
                        </div>
                    </div>
                </label>
                <label class="form-selectgroup-item flex-fill">
                    <input type="checkbox" name="auto_ban_vpn" value="1" class="form-selectgroup-input" @if(env('AUTO_BAN_VPN')==1) checked @endif>
                    <div class="form-selectgroup-label d-flex align-items-center pl-3 pr-3 pt-1 pb-1 p-3 mb-1">
                        <div class="mr-3">
                            <span class="form-selectgroup-check"></span>
                        </div>
                        <div class="form-selectgroup-label-content d-flex align-items-center">
                            <span class="avatar rounded mr-3" style="background-image: url({{asset("img/auto_ban_vpn.png")}})"></span>
                            <div class="lh-sm">
                                <div class="strong text-left">Auto ban VPN user</div>
                                <div class="h5 text-muted text-left">Auto ban who attempts to use VPN connection on offers</div>
                            </div>
                        </div>
                    </div>
                </label>
                <label class="form-selectgroup-item flex-fill">
                    <input type="checkbox" name="auto_ban_root" value="1" class="form-selectgroup-input" @if(env('AUTO_BAN_ROOT')==1) checked @endif>
                    <div class="form-selectgroup-label d-flex align-items-center pl-3 pr-3 pt-1 pb-1 p-3 mb-1">
                        <div class="mr-3">
                            <span class="form-selectgroup-check"></span>
                        </div>
                        <div class="form-selectgroup-label-content d-flex align-items-center">
                            <span class="avatar rounded mr-3" style="background-image: url({{asset("img/auto_ban_rooted.png")}})"></span>
                            <div class="lh-sm">
                                <div class="strong text-left">Auto ban rooted device</div>
                                <div class="h5 text-muted text-left">Auto ban the account who uses rooted device</div>
                            </div>
                        </div>
                    </div>
                </label>
                <label class="form-selectgroup-item flex-fill">
                    <input type="checkbox" name="ban_cc_change" value="1" class="form-selectgroup-input" @if(env('BAN_CC_CHANGE')==1) checked @endif>
                    <div class="form-selectgroup-label d-flex align-items-center pl-3 pr-3 pt-1 pb-1 p-3 mb-1">
                        <div class="mr-3">
                            <span class="form-selectgroup-check"></span>
                        </div>
                        <div class="form-selectgroup-label-content d-flex align-items-center">
                            <span class="avatar rounded mr-3" style="background-image: url({{asset("img/cc_change.png")}})"></span>
                            <div class="lh-sm">
                                <div class="strong text-left">Auto ban for country change</div>
                                <div class="h5 text-muted text-left">Auto ban the account user access the app from different country</div>
                            </div>
                        </div>
                    </div>
                </label>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-dark">Update fraud prevention</button>
            </div>
        </form>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header pt-3 pb-2">
                <h4>Monitor VPN access</h4>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    @foreach($data as $d)
                    <div class="col-6">
                        <div class="card card-body">
                            <a class="row align-items-center" href="{{route('userinfo', ['userid' => $d->userid])}}">
                                <div class="col-auto">
                                    <span class="avatar avatar-md rounded"
                                          style="background-image: url({{$d->avatar}})"></span>
                                </div>
                                <div class="col text-truncate">
                                    <span class="text-body d-block text-truncate">{{$d->name}}</span>
                                    <small class="d-block text-muted text-truncate mt-0">Attempted {{$d->attempted}} times</small>
                                </div>
                                </a>
                                <a href="{{route('frauds_clear', ['id' => $d->id])}}" class="btn-close">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24"
                                     viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z"/>
                                    <line x1="4" y1="7" x2="20" y2="7"/>
                                    <line x1="10" y1="11" x2="10" y2="17"/>
                                    <line x1="14" y1="11" x2="14" y2="17"/>
                                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/></svg>
                            </a>
                            </div>
                        </div>
                    @endforeach
                </div>
        </div>
        <div class="card-footer d-flex align-items-center">
            <p class="m-0 text-muted">Showing <span>{{$data->firstItem()}}</span> to <span>{{$data->lastItem()}}</span>
                of <span>{{$data->total()}}</span> entries</p>
            <ul class="pagination m-0 ml-auto">
                {{ $data->appends(request()->except('page'))->links() }}
            </ul>
        </div>
    </div>
    </div>
    </div>

@endsection
@push('js')
    <script type="text/javascript" src="{{asset("js/jquery-1.11.2.min.js")}}"></script>
    <script type="text/javascript" src="{{asset("js/selectize.min.js")}}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#input-tags').selectize({
                maxItems: 15,
                plugins: ['remove_button'],
                delimiter: ',',
                persist: false,
                create: function (input) {
                    return {
                        value: input,
                        text: input
                    }
                }
            });
        });

    </script>
@endpush

@extends('layouts.head')
@section('css')
<style>
    .bold {
        font-weight: bold !important
    }

</style>
<script type="text/javascript" src="/public/js/jquery-1.11.2.min.js"></script>
@endsection
@section('content')
<div class="row card">
    <form class="p-0" action="{{route('networks_web_update')}}" method="post" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="id" value="{{$data['id']}}">
        <div class="card-header bg-dark-lt h3 text-dark bold pt-2 pb-2">
            <img src="{{$data['image']}}" class="rounded text-truncate img-thumbnail text-small avatar-md mr-2" alt="{{$data['network_name']}}">
            Update {{$data['network_name']}}
        </div>
        <div class="card-body">
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
            <div class="alert alert-info" role="alert">
                <span class="bold mr-2">Allowed macros from App are:</span>
                <span class="text-nowrap mr-2"><span class="text-red">[app_uid]</span> = for User ID</span>
                <span class="text-nowrap mr-2"><span class="text-red">[app_country]</span> = for country ISO</span>
                <span class="text-nowrap mr-2"><span class="text-red">[app_ip]</span> = for user's IP</span>
                <span class="text-nowrap"><span class="text-red">[app_gaid]</span> = for GAID</span>
            </div>
            <div class="row">
                <div class="col col-md-5 col-sm-6 col-12 mb-3">
                    <label class="form-label">Network name:</label>
                    <input type="text" class="form-control" name="network_name" value="{{old('network_name', $data['network_name'])}}">
                </div>
                <div class="col col-md-7 col-sm-6 col-12 mb-3">
                    <label class="form-label">Web offereall URL:</label>
                    <input type="text" class="form-control" name="web_url" value="{{old('web_url', $data['web_url'])}}">
                </div>
                <div class="col col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                    <label class="form-label">Offerwall description:</label>
                    <input type="text" class="form-control" name="offerwall_description" value="{{old('offerwall_description', $data['offerwall_description'])}}">
                </div>
                <div class="col col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                    <div class="form-label">Network logo:</div>
                    <div class="form-file">
                        <input type="file" name="network_image" class="form-file-input img-input" id="imagefile">
                        <label class="form-file-label" for="customFile">
                            <span class="form-file-text img-choose">Choose image...</span>
                            <span class="form-file-button">Browse</span>
                        </label>
                    </div>
                </div>
                <div class="col col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                    <label class="form-label">Offerwall availability</label>
                    <div class="form-selectgroup">
                        <label class="form-selectgroup-item text-no-wrap">
                            <input type="radio" name="enabled" value="1" class="form-selectgroup-input" {{ old('enabled') == '2' ? '' : ($data['enabled'] == '2' ? '' : 'checked') }}>
                            <span class="form-selectgroup-label">Enable</span>
                        </label>
                        <label class="form-selectgroup-item">
                            <input type="radio" name="enabled" value="2" class="form-selectgroup-input" {{ old('enabled') == '2' ? 'checked' : ($data['enabled'] == '2' ? 'checked' : '') }}>
                            <span class="form-selectgroup-label">Disable</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="hr-text mt-4 mb-3 text-blue hr-text-left bold">Postback Setup</div>
            <div class="row">
                <div class="col col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                    <label class="form-label">Parameters visible in URL?</label>
                    <div class="form-selectgroup">
                        <label class="form-selectgroup-item text-no-wrap">
                            <input type="radio" name="postback_type_key" value="1" class="form-selectgroup-input" {{ old('postback_type_key') == '2' ? '' : ($data['postback_type_key'] == '2' ? '' : 'checked') }}>
                            <span class="form-selectgroup-label">Visible</span>
                        </label>
                        <label class="form-selectgroup-item">
                            <input type="radio" name="postback_type_key" value="2" class="form-selectgroup-input" {{ old('postback_type_key') == '2' ? 'checked' : ($data['postback_type_key'] == '2' ? 'checked' : '') }}>
                            <span class="form-selectgroup-label">Hidden</span>
                        </label>
                    </div>
                </div>
                <div class="col col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                    <label class="form-label">URL Secret:</label>
                    <input type="text" class="form-control" name="postback_url_secret_key" value="{{old('postback_url_secret_key', $data['postback_url_secret_key'])}}">
                </div>
                <div class="col col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                    <label class="form-label text-truncate">Parameter for <span class="text-dark bold">Reward Amount:</span></label>
                    <input type="text" class="form-control" name="postback_reward_amount_key" value="{{old('postback_reward_amount_key', $data['postback_reward_amount_key'])}}">
                </div>
                <div class="col col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                    <label class="form-label">Parameter for <span class="text-dark bold">User ID:</span></label>
                    <input type="text" class="form-control" name="postback_user_id_key" value="{{old('postback_user_id_key', $data['postback_user_id_key'])}}">
                </div>
                <div class="col col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                    <label class="form-label">Parameter for <span class="text-dark bold">Offer ID:</span></label>
                    <input type="text" class="form-control" name="postback_offer_id_key" value="{{old('postback_offer_id_key', $data['postback_offer_id_key'])}}">
                </div>
                <div class="col col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                    <label class="form-label">Parameter for <span class="text-dark bold">IP address:</span></label>
                    <input type="text" class="form-control" name="postback_ip_address_key" value="{{old('postback_ip_address_key', $data['postback_ip_address_key'])}}">
                </div>
                <div class="col col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                    <label class="form-label">Parameter for verification <span class="text-danger h5">(if requires)</span>:</label>
                    <input type="text" class="form-control" name="verify" value="{{$data['verify']}}">
                </div>
            </div>
            <div class="d-flex flex-row-reverse mt-4">
                <input type="submit" class="btn btn-dark" value="Update Ad Network" />
                <a href="{{route('networks_web')}}" class="btn btn-white mr-4">Cancel</a>
                <a href="{{route('networks_cpa_del', ['id' => $data['id']])}}" class="btn btn-outline-danger mr-4">Delete Network</a>
            </div>
        </div>
    </form>
</div>

@endsection

@section('javascript')
<script>
    $('.img-input').on('change', function () {
        var fileName = $(this).val().split('\\').pop();
        $(this).closest('.form-file').find('.img-choose').addClass("selected").text(fileName);
    });

</script>
@endsection

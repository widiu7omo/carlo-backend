@extends('layouts.head')
@section('css')
<style>
    .btn-width {
        width: 150px !important
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
    <form class="col-lg-5 col-md-6 mb-3" method="post" action="{{route('game_scratcher_make')}}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="id" value="{{$data['id']}}" />
        <div class="card">
            <div class="card-header bg-primary text-white d-flex">
                @if($data['id'] == 0)
                Add a scratch card
                @else
                Update card settings
                <a href="{{route('game_scratcher_del', ['id' => $data['id']])}}" class="btn btn-white btn-sm ml-auto text-danger">Delete</a>
                @endif
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Card name:</label>
                    <input type="text" class="form-control" name="name" placeholder="Premium Card" value="{{old('name', $data['name'])}}">
                </div>
                <div class="mb-3">
                    <div class="row">
                        <div class="col-6">
                            <label class="form-label">Cost:</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="cost" placeholder="100" value="{{old('cost', $data['cost'])}}">
                                <span class="input-group-text">{{strtolower(env('CURRENCY_NAME'))}}s</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Expire after:</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="days" placeholder="365" value="{{old('days', $data['days'])}}">
                                <span class="input-group-text">days</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Reward range <span class="small text-red">[ in {{strtolower(env('CURRENCY_NAME'))}} ]</span>:</label>
                    <div class="row">
                        <div class="col-6">
                            <div class="input-group">
                                <span class="input-group-text">Min: </span>
                                <input type="text" class="form-control" name="min" placeholder="100" value="{{old('min', $data['min'])}}">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="input-group">
                                <span class="input-group-text">Max: </span>
                                <input type="text" class="form-control" name="max" placeholder="500" value="{{old('max', $data['max'])}}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="row">
                        <div class="col-6">
                            <label class="form-label">Icon image:</label>
                            <div class="form-file form-card">
                                <input type="file" name="img_sm" class="form-file-input img-card">
                                <label class="form-file-label" for="customFile2">
                                    <span class="form-file-text img-sm">Choose image...</span>
                                    <span class="form-file-button">Browse</span>
                                </label>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Scratch Card image:</label>
                            <div class="form-file form-img">
                                <input type="file" name="image" class="form-file-input img-input" id="imagefile">
                                <label class="form-file-label" for="customFile">
                                    <span class="form-file-text img-choose">Choose image...</span>
                                    <span class="form-file-button">Browse</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label">Difficulty level:</label>
                    <input name="difficulty" class="form-range" oninput="modalChange(this.value)" type="range" min="0" max="9" value="{{old('difficulty', $data['difficulty'])}}" step="1">
                </div>
                <div class="mb-4">
                    <label class="form-label">Scratching area coord:</label>
                    <div class="row mb-2">
                        <div class="col-6">
                            <div class="input-group">
                                <span class="input-group-text">Left: </span>
                                <input id="bb_l" type="text" class="form-control" name="coord_l">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="input-group">
                                <span class="input-group-text">Right: </span>
                                <input id="bb_r" type="text" class="form-control" name="coord_r">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="input-group">
                                <span class="input-group-text">Top: </span>
                                <input id="bb_t" type="text" class="form-control" name="coord_t">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="input-group">
                                <span class="input-group-text">Bottom: </span>
                                <input id="bb_b" type="text" class="form-control" name="coord_b">
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="form-check">
                        <input class="form-check-input" type="checkbox" name="can_purchase"  @if(old('can_purchase', $data['can_purchase']) == 0) checked="" @endif>
                        <span class="form-check-label text-red">User cannot purchase this card</span>
                    </label>
                </div>
            </div>
            <div class="card-footer d-flex">
                <button type="submit" class="btn btn-primary mr-auto btn-width">@if($data['id'] == 0)Add this card @else Update card @endif</button>
                <a href="{{route('game_scratch_cat')}}" class="btn btn-secondary btn-width">Back</a>
            </div>
        </div>
    </form>
    <div class="col-lg-7 col-md-6 row d-flex justify-content-center">
        <div class="col-auto col-lg-7 col-md-10 col-sm-8 col-12 mb-3">
            <div class="text-center h3 bg-primary p-2 text-white blink">Set scratching area</div>
            <div class="bg-dark-lt">
                <div id="workarea" class="w-100" style="background-image:url('{{$data['image']}}'); background-size: 100% 100%; background-repeat: no-repeat; "></div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('javascript')
<script>
    $('.img-input').on('change', function () {
        var fileName = $(this).val().split('\\').pop();
        $(this).closest('.form-img').find('.img-choose').addClass("selected").text(fileName);
    });
    $('.img-card').on('change', function () {
        var fileName = $(this).val().split('\\').pop();
        $(this).closest('.form-card').find('.img-sm').addClass("selected").text(fileName);
    });
    var holder = document.getElementById('workarea'),
        cW = holder.clientWidth,
        cH = holder.height = cW * 7 / 4;
    holder.innerHTML = '<canvas id="canvas" width=' + cW + ' height=' + cH + '></canvas>';
    var canvas = document.getElementById('canvas'),
        ctx = canvas.getContext('2d'),
        rects,
        rect = {},
        drag = false,
        mouseX,
        mouseY,
        closeEnough = 5,
        dragTL = dragBL = dragTR = dragBR = false;

    function init() {
        holder.style.backgroundSize = cW + 'px ' + cH + 'px';
        canvas.addEventListener('click', function () {
            if (holder.style.backgroundImage.slice(4, -1).replace(/"/g, "") == '') {
                $('.img-input').click();
            }
        });
        canvas.addEventListener('mousedown', mouseDown);
        canvas.addEventListener('mouseup', mouseUp);
        canvas.addEventListener('mousemove', mouseMove);
        rect = {
            startX: cW * parseFloat("{{old('coord_l', $data['coord'][0])}}") / 100,
            startY: cH * parseFloat("{{old('coord_t', $data['coord'][2])}}") / 100,
            w: cW * parseFloat("{{old('coord_r', $data['coord'][1]) - old('coord_l', $data['coord'][0])}}") / 100,
            h: cH * parseFloat("{{old('coord_b', $data['coord'][3]) - old('coord_t', $data['coord'][2])}}") / 100,
            i_l: document.getElementById('bb_l'),
            i_t: document.getElementById('bb_t'),
            i_r: document.getElementById('bb_r'),
            i_b: document.getElementById('bb_b')
        };
        rect.i_l.addEventListener('input', function () {
            rect.startX = cW * Number(this.value) / 100;
            draw(false);
        });
        rect.i_t.addEventListener('input', function () {
            rect.startY = cH * Number(this.value) / 100;
            draw(false);
        });
        rect.i_r.addEventListener('input', function () {
            rect.w = cW * Number(this.value) / 100 - rect.startX;
            draw(false);
        });
        rect.i_b.addEventListener('input', function () {
            rect.h = cH * Number(this.value) / 100 - rect.startY;
            draw(false);
        });
        draw();
    }

    function mouseDown(e) {
        mouseX = e.pageX - this.offsetLeft;
        mouseY = e.pageY - this.offsetTop;
        if (checkCloseEnough(mouseX, rect.startX) && checkCloseEnough(mouseY, rect.startY)) {
            rects = rect;
            dragTL = true;
        } else if (checkCloseEnough(mouseX, rect.startX + rect.w) && checkCloseEnough(mouseY, rect.startY)) {
            rects = rect;
            dragTR = true;
        } else if (checkCloseEnough(mouseX, rect.startX) && checkCloseEnough(mouseY, rect.startY + rect.h)) {
            rects = rect;
            dragBL = true;
        } else if (checkCloseEnough(mouseX, rect.startX + rect.w) && checkCloseEnough(mouseY, rect.startY + rect.h)) {
            rects = rect;
            dragBR = true;
        }
        draw();
    }

    function checkCloseEnough(p1, p2) {
        return Math.abs(p1 - p2) < closeEnough;
    }

    function mouseUp() {
        dragTL = dragTR = dragBL = dragBR = false;
        if (rects === null) return;
        var pctW = Math.floor(rects.w / canvas.width * 100);
        var pctH = Math.floor(rects.h / canvas.height * 100);
        //alert('width:' + pctW + '% height:' + pctH + "%");
    }

    function mouseMove(e) {
        if (rects === null) return;
        mouseX = e.pageX - this.offsetLeft;
        mouseY = e.pageY - this.offsetTop;
        if (dragTL) {
            rects.w += rects.startX - mouseX;
            rects.h += rects.startY - mouseY;
            rects.startX = mouseX;
            rects.startY = mouseY;
            draw();
        } else if (dragTR) {
            rects.w = Math.abs(rects.startX - mouseX);
            rects.h += rects.startY - mouseY;
            rects.startY = mouseY;
            draw();
        } else if (dragBL) {
            rects.w += rects.startX - mouseX;
            rects.h = Math.abs(rects.startY - mouseY);
            rects.startX = mouseX;
            draw();
        } else if (dragBR) {
            rects.w = Math.abs(rects.startX - mouseX);
            rects.h = Math.abs(rects.startY - mouseY);
            draw();
        }
    }

    function draw(showval = true) {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.fillStyle = "rgba(0,0,0,0.6)";
        ctx.fillRect(rect.startX, rect.startY, rect.w, rect.h);
        drawCircle(rect.startX, rect.startY, closeEnough);
        drawCircle(rect.startX + rect.w, rect.startY, closeEnough);
        drawCircle(rect.startX + rect.w, rect.startY + rect.h, closeEnough);
        drawCircle(rect.startX, rect.startY + rect.h, closeEnough);
        if (showval) {
            rect.i_l.value = ((rect.startX / cW) * 100).toFixed(3);
            rect.i_t.value = ((rect.startY / cH) * 100).toFixed(3);
            rect.i_r.value = ((rect.w + rect.startX) / cW * 100).toFixed(3);
            rect.i_b.value = ((rect.h + rect.startY) / cH * 100).toFixed(3);
        }
    }

    function drawCircle(x, y, radius) {
        ctx.fillStyle = "rgba(255,0,0,0.6)";
        ctx.beginPath();
        ctx.arc(x, y, radius, 0, 2 * Math.PI);
        ctx.fill();
    }

    init();

    function readImage() {
        var fileName = this.value.split('\\').pop();
        document.getElementsByClassName('bg-img').textContent = fileName;
        if (!this.files || !this.files[0]) return;
        const FR = new FileReader();
        FR.addEventListener("load", (evt) => {
            holder.setAttribute("style", 'background-image: url(' + evt.target.result + '); background-size:' + cW + 'px ' + cH + 'px');
        });
        FR.readAsDataURL(this.files[0]);
    }
    document.getElementById('imagefile').addEventListener("change", readImage, false);

</script>
@endsection

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
    <div class="col-lg-4 col-md-6 col-sm-12">
        <form class="card" method="post" action="{{route('game_slot_update')}}">
            @csrf
            <div class="card-header bg-gray-lt text-dark font-weight-bold">Slot Game configuration</div>
            <div class="card-body">
                <div class="row">
                    <div class="mb-3 col-6">
                        <label class="form-label">Cost:</label>
                        <input type="text" class="form-control" name="cost" value="{{env('SLOT_COST')}}">
                    </div>
                    <div class="mb-3 col-6">
                        <label class="form-label">Minimum match:</label>
                        <input type="text" class="form-control" name="min_match" value="{{env('SLOT_MIN_MATCH')}}">
                    </div>
                </div>
                <div class="row">
                    <div class="mb-3 col-6">
                        <label class="form-label">Rows:</label>
                        <input type="text" class="form-control" name="rows" value="{{env('SLOT_ROWS')}}">
                    </div>
                    <div class="mb-3 col-6">
                        <label class="form-label">Columns:</label>
                        <input type="text" class="form-control" name="cols" value="{{env('SLOT_COLS')}}">
                    </div>
                </div>
                <div class="row">
                    <div class="mb-3 col-6">
                        <label class="form-label">Rotation speed:</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="speed" value="{{env('SLOT_SPEED')/1000}}">
                            <span class="input-group-text">sec</span>
                        </div>
                    </div>
                    <div class="mb-3 col-6">
                        <label class="form-label">Daily limit:</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="round" value="{{env('SLOT_DAILY_ROUND')}}">
                            <span class="input-group-text">rounds</span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="mb-3 col-6">
                        <label class="form-label">Total winning lines:</label>
                        <input type="text" class="form-control" name="linecount" value="{{env('SLOT_WINNING_LINES')}}">
                    </div>
                    <div class="mb-3 col-6">
                        <label class="form-label">Total icons:</label>
                        <input type="text" class="form-control" name="icons" value="{{env('SLOT_ICONS')}}">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Icon values <small>(comma seperated)</small>:</label>
                    <input type="text" class="form-control" name="i_val" value="{{env('SLOT_ICON_VAL')}}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Difficulty level:</label>
                    <input name="difficulty" class="form-range" oninput="modalChange(this.value)" type="range" min="0" max="10" value="{{env('SLOT_DIFFICULTY')}}" step="1" class="slider">
                </div>
                <div class="mb-3">
                    <label class="form-label text-blue">Free chance difficulty:</label>
                    <input name="free" class="form-range" oninput="modalChange(this.value)" type="range" min="0" max="4" value="{{env('SLOT_FREE_DIFFICULTY')}}" step="1" class="slider">
                </div>
                <div class="mb-3">
                    <label class="form-label text-info">Which scratch card will win?</label>
                    <div class="input-group">
                        <select name="card_id" class="form-select">
                            @php
                            $ev = env('SLOT_CARD_ID');
                            foreach($data['c'] as $c){
                            if($c->id == $ev){
                            echo '<option value="'.$c->id.'" selected>'.$c->name.'</option>';
                            } else {
                            echo '<option value="'.$c->id.'">'.$c->name.'</option>';
                            }
                            };
                            @endphp
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-info">Scratch card winning difficulty:</label>
                    <input name="card" class="form-range" oninput="modalChange(this.value)" type="range" min="0" max="4" value="{{env('SLOT_CARD_DIFFICULTY')}}" step="1" class="slider">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-block btn-dark">Update</button>
            </div>
        </form>
    </div>
    <div class="card col-lg-8 col-md-6 col-sm-12">
        <div class="card-header">Make your winning lines:</div>
        <div class="card-body text-center">
            @for ($i = 0; $i < env('SLOT_ROWS'); $i++) <div class="mb-3 row">
                @for ($j = 0; $j < env('SLOT_COLS'); $j++) <div class="col-{{floor(12 / env('SLOT_COLS'))}}">
                    <div class="embed-responsive embed-responsive-2by1">
                        <label class="form-selectgroup-item embed-responsive-item">
                            <input type="checkbox" name="counters" id="id_{{$test = $i * env('SLOT_COLS') + $j}}" value="{{$test}}" onchange="addItem(this)" class="form-selectgroup-input">
                            <span class="form-selectgroup-label h-100 d-flex justify-content-center line-paddings"></span>
                        </label>
                    </div>
        </div>
        @endfor
    </div>
    @endfor
    <div>
        @for ($k = 0; $k < env('SLOT_WINNING_LINES'); $k++) <span class="step"></span> @endfor
    </div>
</div>
<div class="card-footer text-center">
    <button type="button" id="prevBtn" class="btn btn-secondary mr-3 px-5" onclick="prev()">Previous</button>
    <button type="button" id="nextBtn" class="btn btn-primary px-6" onclick="next()">Next</button>
</div>
</div>
</div>
<div class="modal modal-blur fade" id="infodiag" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-title text-primary">Winning line setup mismatches!</div>
                <div>You need to setup your winning lines as per you new game configuration.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary ml-auto" data-dismiss="modal">Okay</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('css')
<style>
    .line-paddings {
        border: 3px solid #ddd;
        text-align: center;
    }

    .step {
        height: 15px;
        width: 15px;
        margin: 0 2px;
        background-color: #00f;
        border: none;
        border-radius: 50%;
        display: inline-block;
        opacity: 0.2;
    }

    /* Mark the active step: */
    .step.active {
        opacity: 1;
    }

    /* Mark the steps that are finished and valid: */
    .step.finish {
        background-color: #4CAF50;
    }

</style>
<meta name="csrf-token" content="{{csrf_token()}}" />
<script type="text/javascript" src="{{asset("js/jquery-1.11.2.min.js")}}"></script>
@endsection
@section('javascript')
<script>
    var nxtBtn = document.getElementById("nextBtn");
    var counts = "{{env('SLOT_WINNING_LINES')}}";
    var rows = parseInt("{{env('SLOT_ROWS')}}");
    var cols = parseInt("{{env('SLOT_COLS')}}");
    var currentTab = 0;
    window.addEventListener('load', function () {
        fixStepIndicator(0);
    }, false);
    var fval = JSON.parse("{{$data['l']}}".replace(/&quot;/g, ''));
    var valz = [];
    if (typeof fval[currentTab] != "undefined") {
        setValues(fval[currentTab]);
    }
    document.addEventListener('DOMContentLoaded', function () {
        if (fval.length == 0) {
            $('#infodiag').modal({
                backdrop: "static"
            });
        }
    }, false);

    function next() {
        fval[currentTab] = getValues();
        if (fval[currentTab].length < "{{env('SLOT_MIN_MATCH')}}") {
            alert('Minimum ' + "{{env('SLOT_MIN_MATCH')}}" + ' selection is required!');
            return;
        }
        if (currentTab + 2 == counts) {
            currentTab++;
            nxtBtn.setAttribute('class', 'btn btn-success px-6');
            nxtBtn.textContent = 'Finish';
            fixStepIndicator(currentTab);
            if (typeof fval[currentTab] != "undefined") {
                setValues(fval[currentTab]);
            }
        } else if (currentTab + 1 == counts) {
            var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute("content");
            var myForm = document.createElement('form');
            myForm.setAttribute('action', "{{route('game_slot_line_add')}}");
            myForm.setAttribute('method', 'post');
            myForm.setAttribute('hidden', 'true');
            myForm.appendChild(appendData('_token', csrf));
            myForm.appendChild(appendData('rows', rows));
            myForm.appendChild(appendData('cols', cols));
            myForm.appendChild(appendData('lines', JSON.stringify(fval)));
            document.body.appendChild(myForm);
            myForm.submit();
        } else {
            currentTab++;
            nxtBtn.setAttribute('class', 'btn btn-primary px-6');
            nxtBtn.textContent = 'Next';
            fixStepIndicator(currentTab);
            if (typeof fval[currentTab] != "undefined") {
                setValues(fval[currentTab]);
            }
        }
    }

    function appendData(key, val) {
        var myInput = document.createElement('input');
        myInput.setAttribute('type', 'text');
        myInput.setAttribute('name', key);
        myInput.setAttribute('value', val);
        return myInput;
    }

    function prev() {
        if (currentTab != 0) {
            currentTab--;
            fixStepIndicator(currentTab);
            if (currentTab + 1 < counts) {
                nxtBtn.setAttribute('class', 'btn btn-primary px-6');
                nxtBtn.textContent = 'Next';
            }
            setValues(fval[currentTab]);
        }
    }

    function fixStepIndicator(n) {
        var i, x = document.getElementsByClassName("step");
        for (i = 0; i < x.length; i++) {
            x[i].className = x[i].className.replace(" active", "");
        }
        x[n].className += " active";
    }

    function addItem(obj) {
        if (obj.checked) {
            if (!valz.includes(obj.value)) {
                valz.push(obj.value);
            }
        } else if (valz.includes(obj.value)) {
            vl = [];
            for (var i = 0; i < valz.length; i++) {
                if (valz[i] !== obj.value) {
                    vl.push(valz[i]);
                }
            }
            valz = vl;
        }
    }

    function getValues() {
        var valy = [];
        for (var i = 0; i < valz.length; i++) {
            var x = document.getElementById("id_" + valz[i]);
            x.checked = false;
            valy.push(valz[i]);
        }
        valz = [];
        return valy;
    }

    function setValues(val) {
        var x = document.getElementsByName("counters");
        x.forEach((e) => {
            e.checked = false;
        });
        valz = [];
        for (var i = 0; i < val.length; i++) {
            var y = document.getElementById("id_" + val[i]);
            y.checked = true;
            valz.push(val[i]);
        }
    }

</script>
@endsection

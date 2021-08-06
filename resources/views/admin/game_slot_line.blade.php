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
    <div class="card col-lg-8 col-md-10">
        <div class="card-header">Make your winning lines for <span class="font-weight-bold ml-1">{{$data['name']}}</span>:</div>
        <div class="card-body text-center">
            @for ($i = 0; $i < $data['rows']; $i++) <div class="mb-3 row">
                @for ($j = 0; $j < $data['cols']; $j++) <div class="col-{{floor(12 / $data['cols'])}}">
                    <div class="embed-responsive embed-responsive-2by1">
                        <label class="form-selectgroup-item embed-responsive-item">
                            <input type="checkbox" name="counters" value="{{$i * $data['cols'] + $j}}" class="form-selectgroup-input">
                            <span class="form-selectgroup-label h-100 d-flex justify-content-center line-paddings"></span>
                        </label>
                    </div>
        </div>
        @endfor
    </div>
    @endfor
    <div>
        @for ($k = 0; $k < $data['count']; $k++) <span class="step"></span> @endfor
    </div>
</div>
<div class="card-footer text-center">
    @if (\Session::has('success'))
    <a href="{{ url()->previous() }}" class="btn btn-success mr-3 px-5">Go back</a>
    @else
    <button type="button" id="prevBtn" class="btn btn-secondary mr-3 px-5" onclick="prev()">Previous</button>
    <button type="button" id="nextBtn" class="btn btn-primary px-6" onclick="next()">Next</button>
    @endif
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
@endsection
@section('javascript')
<script>
    var nxtBtn = document.getElementById("nextBtn");
    var counts = '{{$data["count"]}}';
    var currentTab = 0;
    window.addEventListener('load', function () {
        fixStepIndicator(0);
    }, false);
    var fval = [];

    function next() {
        fval[currentTab] = getValues();
        if (fval[currentTab].length < 3) {
            alert('Minimum 3 selection is required!');
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
            myForm.appendChild(appendData('lines', JSON.stringify(fval)));
            myForm.appendChild(appendData('name', "{{$data['name']}}"));
            myForm.appendChild(appendData('rows', "{{$data['rows']}}"));
            myForm.appendChild(appendData('cols', "{{$data['cols']}}"));
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

    function getValues() {
        var val = [];
        var x = document.getElementsByName("counters");
        x.forEach((e) => {
            if (e.checked) {
                val.push(e.value);
                e.checked = false;
            }
        });
        return val;
    }

    function setValues(val) {
        var x = document.getElementsByName("counters");
        x.forEach((e) => {
            e.checked = false;
        });
        for (var i = 0; i < val.length; i++) {
            x[val[i]].checked = true;
        }
    }

</script>
@endsection

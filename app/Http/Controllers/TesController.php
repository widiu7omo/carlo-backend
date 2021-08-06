<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;

class TesController extends Controller
{
    public function Index()
    {
        Log::debug("From Test Controller");
        return view("tes");
    }
}

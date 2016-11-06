<?php

namespace App\Http\Controllers;

use App\Band;
use App\Song;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Socialite;


class RateController extends Controller
{
    public function index()
	{
		$bands=Band::all();
		$songs=Song::with('band')->get();
		return view('rate.index')->with(['bands'=>$bands,'songs'=>$songs]);
	}
}
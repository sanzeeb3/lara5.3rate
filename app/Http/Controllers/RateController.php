<?php

namespace App\Http\Controllers;

use App\Band;
use App\Song;
use App\Test;
use App\Post;
use App\User;
use Corcel;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Socialite;
use DB;

class RateController extends Controller
{
    public function index()
	{    
   
		$bands=Band::where('id','<>',6)->get();
     	$songs=Song::with('band')->orderBy('views','desc')->get();

		return view('rate.index')->with(['bands'=>$bands,'songs'=>$songs]);
	}

	public function uploadfile()
    { 	
        $target_dir = 'C:\xampp\htdocs\lara5.3\public\newuploads';
        $tmpname = $_FILES["file"]["tmp_name"];
        $temp = explode(".", $_FILES["file"]["name"]);
        $newfilename = round(microtime(true)) . '.' . end($temp);
        $target_file= $target_dir.DIRECTORY_SEPARATOR.basename($newfilename);
        if(move_uploaded_file($tmpname, $target_file)){
            echo json_encode($newfilename);die;
        }
        else
        {
            echo json_encode(false);die;
        }
    }

    public function login()
    { 
        return Socialite::driver('facebook')->redirect();
    }

    public function logout()
    { 
        Auth::logout();
        return redirect('/')->with('message','logged out!');
    }

    public function callback()
    {
        $user = Socialite::driver('facebook')->user(); 
        $newUser = User::firstOrCreate(
            ['name' => $user->name, 'email' => $user->email, 'avatar' => $user->avatar,'verified'=>1],
            ['remember_token' => $user->token,'password'=> str_shuffle('abcdgfdfDSFdsdf34234DSGasdefgh122434567890xsY')]
        );

        Auth::login($newUser, true);
        return redirect('/');
        
    }

    public function add(Request $request)
    {
        if(!Auth::check())
        {
            echo json_encode("notloggedin");die;
        }
       if($request->Ajax())
       { 
            if(!$request->all())
            {
                echo json_encode(FALSE); die;
            }
            else
            {
                $newSong = new Song();
                $newSong->name=$request->song;
                $newSong->added_by=Auth::user()->name;

                if($request->band)
                {
                    $newBand= new Band();
                    $newBand->name=$request->band;
                    $newBand->save();
                    $newSong->band_id=$newBand->id;
                }
                else
                {
                    $newSong->band_id=$request->selectBand;
                }

                $newSong->link=$request->link;
                $newSong->mp3=$request->uploadedfile;
                if($newSong->save())
                {
                    echo json_encode(TRUE); die;
                }
            }

            echo json_encode(FALSE); die;
        }
    }

    public function checkBand(Request $request)
    {
        if($request->ajax())
        {
            $check=Band::where(['name'=>$_POST['band']])->first();
            if(!$check)
            {
                echo json_encode(TRUE);die;
            }

            echo json_encode(FALSE);die;
        }   
    }

    public function rate(Request $request)
    {
       if($request->id && $request->rate)
        {
            DB::table('songs')->where('id',$request->id)->increment('views',$request->rate);
            return redirect('/');
        }
    }

}
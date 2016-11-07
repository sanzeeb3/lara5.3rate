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

    public function add(Request $request)
    {
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
}
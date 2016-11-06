<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Song extends model 
{

    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $fillable = ['name','views'];

    public function band()
    {
        return $this->belongsTo('App\Band'); 
    }
}

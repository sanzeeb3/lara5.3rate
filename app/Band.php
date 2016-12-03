<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Band extends model 
{

    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $table = 'bands';
    protected $fillable = ['name','views'];

 	public function songs()
    {
        return $this->hasMany('App\Song'); 
    }

}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Website extends Model
{
    protected $fillable = ['url', 'name'];

    public function status()
    {
    	return $this->hasMany(Status::class);
    }
}

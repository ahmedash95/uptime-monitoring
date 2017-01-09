<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
	protected $table = 'status';
    protected $fillable = ['status', 'response_time'];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    protected $table = 'friends';
    public $timestamps = false;
    protected $fillable = ['user_id', 'target_id'];
}

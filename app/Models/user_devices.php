<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user_devices extends Model
{
    use HasFactory;
    protected $table = "user_devices";
    protected $fillable = [
        'user_id' , 'device_id','date','device_name','browser'
    ];
}

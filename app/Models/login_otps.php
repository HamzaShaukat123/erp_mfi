<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class login_otps extends Model
{
    use HasFactory;
    protected $table = "login_otps";
    protected $fillable = [
        'user_id','otp',
    ];
}

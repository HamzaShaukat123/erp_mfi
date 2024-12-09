<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;

class users extends Authenticatable
{
    use HasFactory;
    protected $table = "users";
    protected $fillable = [
        'name', 'email', 'cnic_no','phone_no', 'address', 'date', 'username', 
        'password','cnic_front','cnic_back','picture',
    ];

}

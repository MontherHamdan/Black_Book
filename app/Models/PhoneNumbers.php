<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhoneNumbers extends Model
{
    use HasFactory;

    protected $table = "phone_numbers";

    protected $fillable = ['phone_number'];
}

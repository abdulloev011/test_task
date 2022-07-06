<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'plantation_year',
        'number_of_tree',
        'currency',
        'price',
        'is_active',
        'unique_code'
    ];
}

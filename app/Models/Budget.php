<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'id_users',
        'id_tipe_expenses',
        'budget',
        'created_at',
        'updated_at'
    ];
}

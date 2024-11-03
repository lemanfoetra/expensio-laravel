<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_tipe_expense',
        'id_users',
        'date',
        'nominal',
        'deskripsi',
        'created_at',
        'updated_at'
    ];

    public function tipeExpense()
    {
        return $this->hasOne(TipeExpense::class, 'id', 'id_tipe_expense');
    }
}

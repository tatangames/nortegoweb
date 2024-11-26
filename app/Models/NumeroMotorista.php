<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NumeroMotorista extends Model
{
    use HasFactory;
    protected $table = 'numero_motoristas';
    public $timestamps = false;
}

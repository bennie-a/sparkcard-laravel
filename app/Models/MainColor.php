<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MainColor extends Model
{
    use HasFactory;

    protected $table = 'main_color';

    public $fillable = ['attr', 'name'];

    public $primaryKey = 'attr';

    public $incrementing = false;

    public $keyType = 'string';
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MainColor extends Model
{
    use HasFactory;

    protected $table = 'main_color';

    protected $fillable = ['attr', 'name'];

    protected $primaryKey = 'attr';

    protected $incrementing = false;

    protected $keyType = 'string';
}

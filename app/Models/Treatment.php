<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
/**
 * treatmentテーブルのModelクラス
 */
class Treatment extends Model
{
    use HasFactory;

    protected $table = 'treatment';

    protected $fillable = ['attr',  'name'];

}

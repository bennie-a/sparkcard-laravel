<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaseToken extends Model
{
    use HasFactory;

    protected $table = 'base_token';

    protected $fillable = [
        'access_token',
        'refresh_token',
        'expires_in',
    ];

    // 暗号化
    protected $casts = [
        'access_token' => 'encrypted',
        'refresh_token' => 'encrypted',
        'expires_in' => 'datetime',
    ];
}

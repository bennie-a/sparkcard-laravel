<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardInfo extends Model
{
    use HasFactory;
    protected $table = 'card_info';

    public function expansion()
    {
        return $this->belongsTo('App\Expansion');
    }

    protected $fillable = ['exp_id', 'barcode','name', 'en_name', 'number', 'color_id', 'image_url'];
}

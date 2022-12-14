<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Expansion extends Model
{
    use HasFactory;

    protected $table = 'expansion';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['notion_id', 'base_id', 'name', 'attr', 'release_date'];

    public function cardinfo()
    {
        return $this->hasMany('App\CardInfo');
    }

    public static function isExist($name) {
        return DB::table("expansion")->where('name', $name)->exists();
    }

}

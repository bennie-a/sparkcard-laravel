<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\Constant\StockpileHeader as Header;

class ArrivalLog extends Model
{
    protected $table = 'arrival_log';

    protected $fillable = ['id', 'stock_id',  Header::ARRIVAL_DATE, Header::QUANTITY,
                                            Header::COST, Header::VENDOR_TYPE_ID, Header::VENDOR];

    use HasFactory;
}

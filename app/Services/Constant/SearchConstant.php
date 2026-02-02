<?php
namespace App\Services\Constant;

use Deprecated;

class SearchConstant {
    const CARD_NAME = 'card_name';
    const SET_NAME = 'set_name';
    const LIMIT = 'limit';

    #[Deprecated(message:'GlobalConstantに移動', since:'5.1.0')]
    const STATUS = 'status';
    const PRICE = 'price';

    // 開始日
    const START_DATE = 'start_date';
    // 終了日
    const END_DATE = 'end_date';

    // 取引先カテゴリID    
    const VENDOR_TYPE_ID = 'vendor_type_id';

}
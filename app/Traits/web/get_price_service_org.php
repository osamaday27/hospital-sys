<?php
namespace App\Traits\Web;

use App\models\finance\contract_last_level;
use App\models\finance\contract_frist_level;
use App\models\finance\contract_second_level;
use App\models\finance\contracts_details_last_level;
use App\models\finance\price_lists;
use App\models\finance\pricelistservice;

trait get_price_service_org
{
   public function getPrice($contract_id, $service_id)
    {
        $contract_last_level = contract_last_level::find($contract_id);
        if (!$contract_last_level) {
            return 'العقد المحدد غير موجود.';
        }

        $contract_second_level = contract_second_level::find($contract_last_level->second_level_id);
        if (!$contract_second_level) {
            return 'المستوى الثاني من العقد غير موجود.';
        }

        $contract_frist_level = contract_frist_level::find($contract_second_level->frist_level_id);
        if (!$contract_frist_level) {
            return 'المستوى الأول من العقد غير موجود.';
        }

        $price_lists = price_lists::find($contract_frist_level->price_list_id);
        if (!$price_lists) {
            return 'قائمة الأسعار المرتبطة بالعقد غير موجودة.';
        }

        $hospital_price = pricelistservice::where('service_id', $service_id)
            ->where('price_list_id', $price_lists->id)
            ->value('hospital_price');

        if (!$hospital_price) {
            return 'لا يوجد تسعير لهذه الخدمة في قائمة الأسعار المحددة.';
        }

        return [
            'hospital_price' => $hospital_price
        ];
    }
}
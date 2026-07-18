<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Medical\{MedicationUnit, MedicationDuration, MedicationTaken};

class MedicationDataSeeder extends Seeder
{
    public function run()
    {
        $this->seedUnits();
        $this->seedDurations();
        $this->seedTakens();
    }

    private function seedUnits()
    {
        $units = [
            [
                'name' => ['ar' => 'قرص', 'en' => 'Tablet'],
                'abbreviation' => ['ar' => 'قرص', 'en' => 'Tab'],
                'is_active' => true,
            ],
            [
                'name' => ['ar' => 'كبسولة', 'en' => 'Capsule'],
                'abbreviation' => ['ar' => 'كبسولة', 'en' => 'Cap'],
                'is_active' => true,
            ],
            [
                'name' => ['ar' => 'قطرة', 'en' => 'Drop'],
                'abbreviation' => ['ar' => 'قطرة', 'en' => 'Drop'],
                'is_active' => true,
            ],
            [
                'name' => ['ar' => 'ملليلتر', 'en' => 'Milliliter'],
                'abbreviation' => ['ar' => 'مل', 'en' => 'ml'],
                'is_active' => true,
            ],
            [
                'name' => ['ar' => 'ملعقة صغيرة', 'en' => 'Teaspoon'],
                'abbreviation' => ['ar' => 'م.ص', 'en' => 'tsp'],
                'is_active' => true,
            ],
            [
                'name' => ['ar' => 'ملعقة كبيرة', 'en' => 'Tablespoon'],
                'abbreviation' => ['ar' => 'م.ك', 'en' => 'tbsp'],
                'is_active' => true,
            ],
            [
                'name' => ['ar' => 'أمبول', 'en' => 'Ampoule'],
                'abbreviation' => ['ar' => 'أمبول', 'en' => 'Amp'],
                'is_active' => true,
            ],
            [
                'name' => ['ar' => 'حقنة', 'en' => 'Injection'],
                'abbreviation' => ['ar' => 'حقنة', 'en' => 'Inj'],
                'is_active' => true,
            ],
            [
                'name' => ['ar' => 'بخاخ', 'en' => 'Spray'],
                'abbreviation' => ['ar' => 'بخاخ', 'en' => 'Spray'],
                'is_active' => true,
            ],
            [
                'name' => ['ar' => 'مرهم', 'en' => 'Ointment'],
                'abbreviation' => ['ar' => 'مرهم', 'en' => 'Oint'],
                'is_active' => true,
            ],
            [
                'name' => ['ar' => 'كريم', 'en' => 'Cream'],
                'abbreviation' => ['ar' => 'كريم', 'en' => 'Cream'],
                'is_active' => true,
            ],
            [
                'name' => ['ar' => 'جل', 'en' => 'Gel'],
                'abbreviation' => ['ar' => 'جل', 'en' => 'Gel'],
                'is_active' => true,
            ],
            [
                'name' => ['ar' => 'لصقة', 'en' => 'Patch'],
                'abbreviation' => ['ar' => 'لصقة', 'en' => 'Patch'],
                'is_active' => true,
            ],
            [
                'name' => ['ar' => 'شراب', 'en' => 'Syrup'],
                'abbreviation' => ['ar' => 'شراب', 'en' => 'Syr'],
                'is_active' => true,
            ],
        ];

        foreach ($units as $unit) {
            MedicationUnit::firstOrCreate(
                ['name->ar' => $unit['name']['ar']],
                $unit
            );
        }
    }

    private function seedDurations()
    {
        $durations = [
            ['name' => ['ar' => '3 أيام', 'en' => '3 Days'], 'days' => 3, 'is_active' => true],
            ['name' => ['ar' => '5 أيام', 'en' => '5 Days'], 'days' => 5, 'is_active' => true],
            ['name' => ['ar' => '7 أيام', 'en' => '7 Days'], 'days' => 7, 'is_active' => true],
            ['name' => ['ar' => '10 أيام', 'en' => '10 Days'], 'days' => 10, 'is_active' => true],
            ['name' => ['ar' => 'أسبوعين', 'en' => '2 Weeks'], 'days' => 14, 'is_active' => true],
            ['name' => ['ar' => '3 أسابيع', 'en' => '3 Weeks'], 'days' => 21, 'is_active' => true],
            ['name' => ['ar' => 'شهر', 'en' => '1 Month'], 'days' => 30, 'is_active' => true],
            ['name' => ['ar' => 'شهرين', 'en' => '2 Months'], 'days' => 60, 'is_active' => true],
            ['name' => ['ar' => '3 أشهر', 'en' => '3 Months'], 'days' => 90, 'is_active' => true],
            ['name' => ['ar' => 'حتى انتهاء الكمية', 'en' => 'Until Finished'], 'days' => null, 'is_active' => true],
            ['name' => ['ar' => 'حسب الحاجة', 'en' => 'As Needed'], 'days' => null, 'is_active' => true],
            ['name' => ['ar' => 'استمرار', 'en' => 'Continuous'], 'days' => null, 'is_active' => true],
        ];

        foreach ($durations as $duration) {
            MedicationDuration::firstOrCreate(
                ['name->ar' => $duration['name']['ar']],
                $duration
            );
        }
    }

    private function seedTakens()
    {
        $takens = [
            [
                'name' => ['ar' => 'بالفم', 'en' => 'Oral'],
                'description' => ['ar' => 'يؤخذ عن طريق الفم', 'en' => 'Taken by mouth'],
                'is_active' => true,
            ],
            [
                'name' => ['ar' => 'قطرة للعين', 'en' => 'Eye Drop'],
                'description' => ['ar' => 'توضع في العين', 'en' => 'Applied to the eye'],
                'is_active' => true,
            ],
            [
                'name' => ['ar' => 'قطرة للأذن', 'en' => 'Ear Drop'],
                'description' => ['ar' => 'توضع في الأذن', 'en' => 'Applied to the ear'],
                'is_active' => true,
            ],
            [
                'name' => ['ar' => 'قطرة للأنف', 'en' => 'Nasal Drop'],
                'description' => ['ar' => 'توضع في الأنف', 'en' => 'Applied to the nose'],
                'is_active' => true,
            ],
            [
                'name' => ['ar' => 'حقن وريدي', 'en' => 'Intravenous (IV)'],
                'description' => ['ar' => 'يحقن في الوريد', 'en' => 'Injected into vein'],
                'is_active' => true,
            ],
            [
                'name' => ['ar' => 'حقن عضلي', 'en' => 'Intramuscular (IM)'],
                'description' => ['ar' => 'يحقن في العضل', 'en' => 'Injected into muscle'],
                'is_active' => true,
            ],
            [
                'name' => ['ar' => 'حقن تحت الجلد', 'en' => 'Subcutaneous (SC)'],
                'description' => ['ar' => 'يحقن تحت الجلد', 'en' => 'Injected under skin'],
                'is_active' => true,
            ],
            [
                'name' => ['ar' => 'موضعي على الجلد', 'en' => 'Topical'],
                'description' => ['ar' => 'يدهن على الجلد', 'en' => 'Applied to skin'],
                'is_active' => true,
            ],
            [
                'name' => ['ar' => 'استنشاق', 'en' => 'Inhalation'],
                'description' => ['ar' => 'يستنشق عن طريق الفم أو الأنف', 'en' => 'Inhaled through mouth or nose'],
                'is_active' => true,
            ],
            [
                'name' => ['ar' => 'تحاميل شرجية', 'en' => 'Rectal Suppository'],
                'description' => ['ar' => 'توضع في فتحة الشرج', 'en' => 'Inserted into rectum'],
                'is_active' => true,
            ],
            [
                'name' => ['ar' => 'تحاميل مهبلية', 'en' => 'Vaginal Suppository'],
                'description' => ['ar' => 'توضع في المهبل', 'en' => 'Inserted into vagina'],
                'is_active' => true,
            ],
            [
                'name' => ['ar' => 'تحت اللسان', 'en' => 'Sublingual'],
                'description' => ['ar' => 'يوضع تحت اللسان', 'en' => 'Placed under tongue'],
                'is_active' => true,
            ],
            [
                'name' => ['ar' => 'بين الخد واللثة', 'en' => 'Buccal'],
                'description' => ['ar' => 'يوضع بين الخد واللثة', 'en' => 'Placed between cheek and gum'],
                'is_active' => true,
            ],
            [
                'name' => ['ar' => 'غسول فموي', 'en' => 'Mouth Wash'],
                'description' => ['ar' => 'يستخدم كغسول للفم', 'en' => 'Used as mouth rinse'],
                'is_active' => true,
            ],
            [
                'name' => ['ar' => 'لبوس', 'en' => 'Suppository'],
                'description' => ['ar' => 'يستخدم كتحميلة', 'en' => 'Used as suppository'],
                'is_active' => true,
            ],
        ];

        foreach ($takens as $taken) {
            MedicationTaken::firstOrCreate(
                ['name->ar' => $taken['name']['ar']],
                $taken
            );
        }
    }
}
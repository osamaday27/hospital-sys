<?php

namespace App\Imports;

use App\models\administrative\medical_record;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class PatientImport implements ToModel, WithHeadingRow, WithChunkReading
{
    public function model(array $row)
    {
        // يمكنك هنا تقسيم الاسم إلى أجزاء عربية وإنجليزية
        $nameParts = explode(' ', $row['full_name']);
        
        // ارسال طلب لترجمة الأسماء إذا لزم الأمر
        $translatedNames = $this->translateNames($nameParts);

        // إنشاء سجل جديد للمريض في قاعدة البيانات
        return new medical_record([
            'medical_number' => $row['medical_number'],
            'full_name' => $row['full_name'],
            'first_name_ar' => $nameParts[0] ?? '',
            'second_name_ar' => $nameParts[1] ?? '',
            'third_name_ar' => $nameParts[2] ?? '',
            'fourth_name_ar' => $nameParts[3] ?? '',
            'first_name_en' => $translatedNames[0] ?? '',
            'second_name_en' => $translatedNames[1] ?? '',
            'third_name_en' => $translatedNames[2] ?? '',
            'fourth_name_en' => $translatedNames[3] ?? '',
            'date_of_birth' => $row['date_of_birth']?? '',
            'gender' => $row['gender']?? '',
            'national_id' => $row['national_id']?? '',
            'passport' => $row['passport']?? '',
            'marital_status' => $row['marital_status']?? '',
            'phone_number' => $row['phone_number']?? '',
            'phone_number2' => $row['phone_number2']?? '',
            'whatsapp' => $row['whatsapp']?? '',
            'nationality' => $row['nationality']?? '',
            'country' => $row['id_country']?? '',
            'governorate' => $row['id_governmentes']?? '',
            'city' => $row['id_city']?? '',
            'area' => $row['id_area']?? '',
            'full_address' => $row['full_address']?? '',
        ]);
    }

    public function chunkSize(): int
    {
        return 1000; // يمكنك تعديل حجم القطعة حسب الحاجة
    }

    private function translateNames(array $nameParts)
    {
        // هنا يمكنك تنفيذ الترجمات المطلوبة عبر API أو من خلال خوارزميات أخرى
        // على سبيل المثال باستخدام خدمة ترجمة عبر API
        $translatedNames = [];

        foreach ($nameParts as $part) {
            // في هذه الحالة الترجمة تعتمد على API أو خوارزمية أخرى
            // هذا مثال عام لترجمة الأسماء، يمكن تعديله
            $translatedNames[] = $part; // الترجمة الحقيقية تضاف هنا
        }

        return $translatedNames;
    }
}
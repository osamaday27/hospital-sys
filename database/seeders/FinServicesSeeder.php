<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB; // إضافة استيراد DB

class FinServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        foreach (range(1, 1550) as $index) {
            DB::table('fin_services')->insert([
                'sublevel_id' => $faker->numberBetween(1, 3),
                'name' => $faker->word,
                'hospital_code' => $faker->bothify('HOSP###'),
                'description' => $faker->sentence,
                'patient_type' => $faker->randomElement(['Adult', 'Child', 'Senior']),
                'age_restriction_from' => $faker->numberBetween(0, 18),
                'age_restriction_to' => $faker->numberBetween(19, 70),
                'repeat_duration' => $faker->randomElement(['Monthly', 'Yearly', 'Weekly']),
                'confidentiality' => $faker->randomElement(['High', 'Medium', 'Low']),
                'status' => $faker->randomElement(['active', 'inactive']),
                'default_branch' => $faker->word,
                'user_id' => 1,  // يمكنك تخصيص المستخدم حسب الحاجة
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OfficialHoliday;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class OfficiaHolidaysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $holidays = [
            ['title' => 'عيد الميلاد المجيد', 'from' => '2025-01-07','to'=>null],
            ['title' => 'ثورة ٢٥ يناير وعيد الشرطة', 'from' => '2025-01-25','to'=>null],
            ['title' => 'عيد الفطر المبارك', 'from' => '2025-03-29','to'=>'2025-04-03'],
            ['title' => 'عيد تحرير سيناء (٢٥ أبريل ١٩٨٢)', 'from' => '2025-04-25','to'=>null],
            ['title' => 'عيد العمال', 'from' => '2025-05-05','to'=>null],
            ['title' => 'عيد شم النسيم', 'from' => '2025-05-06','to'=>null],
            ['title' => 'عيد الأضحى المبارك', 'from' => '2025-06-04','to'=>'2025-06-9'],
            ['title' => 'ثورة ٣٠ يونيو', 'from' => '2025-06-30','to'=>null],
            ['title' => 'رأس السنة الهجرية', 'from' => '2025-06-30','to'=>null],
            ['title' => 'ثورة ٢٣ يوليو ١٩٥٢', 'from' => '2025-07-23','to'=>null],
            ['title' => 'المولد النبوي الشريف', 'from' => '2025-09-04','to'=>null],
            ['title' => 'عيد القوات المسلحة (٦ أكتوبر ١٩٧٣)', 'from' => '2025-10-06','to'=>null],
           
            // Add more records as needed
        ];
        
        // Use the insert method to insert multiple records
        OfficialHoliday::insert($holidays);
       
    }
}
<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(ProductSeeder::class);
        $this->call(CouponSeeder::class);
        // $this->call(ClientCodeSeeder::class);
        $this->call(PromotionSeeder::class);
        $this->call(SettingSeeder::class);
        $this->call(ImageSeeder::class);
        $this->call(LandingBlockSeeder::class);
        $this->call(UserSeeder::class);
    }
}

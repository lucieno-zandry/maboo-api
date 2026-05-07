<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // database/seeders/SettingSeeder.php
        Setting::create([
            'key' => 'maintenance_mode',
            'value' => false,
            'type' => 'boolean',
            'group' => 'Maintenance',
            'label' => 'Maintenance Mode',
            'description' => 'When enabled, the site will be unavailable to non-admin users.',
            'is_public' => true,
        ]);

        Setting::create([
            'key' => 'currency',
            'value' => 'MGA',
            'type' => 'string',
            'group' => 'General',
            'label' => 'Currency',
            'description' => 'Default currency for the store.',
            'is_public' => true,
        ]);

        Setting::create([
            'key' => 'app_name',
            'value' => 'Maboo',
            'type' => 'string',
            'group' => 'General',
            'label' => 'App name',
            'description' => 'Name of the application to display.',
            'is_public' => true,
        ]);

        Setting::create([
            'key' => 'contact_email',
            'value' => 'contact@maboo.mg',
            'type' => 'string',
            'group' => 'General',
            'label' => 'Contact Email',
            'description' => 'Email that customers can contact.',
            'is_public' => true,
        ]);

        Setting::create([
            'key' => 'app_logo',
            'value' => 'https://www.svgrepo.com/show/525334/eye.svg',
            'type' => 'string',
            'group' => 'General',
            'label' => 'Application Logo URL',
            'description' => 'The url of the logo of the application.',
            'is_public' => true,
        ]);

        Setting::create([
            'key' => 'default_country',
            'value' => 'MG',
            'type' => 'string',
            'group' => 'Location and Delivery',
            'label' => 'Default country',
            'description' => 'Fallback to this country to search shipping methods available.',
            'is_public' => true,
        ]);

        Setting::create([
            'key' => 'default_city',
            'value' => 'Antananarivo',
            'type' => 'string',
            'group' => 'Location and Delivery',
            'label' => 'Default City',
            'description' => 'Fallback to this city to search shipping methods available.',
            'is_public' => true,
        ]);

        Setting::create([
            'key' => 'currency_enabled',
            'value' => false,
            'type' => 'boolean',
            'group' => 'Internationalization',
            'label' => 'Enable currency',
            'description' => 'choose whether if customers can choose another currency.',
            'is_public' => true,
        ]);

        Setting::create([
            'key' => 'client_code_enabled',
            'value' => false,
            'type' => 'boolean',
            'group' => 'Marketing',
            'label' => 'Enable customer codes',
            'description' => 'When enabled, customers can enter a client code and access special discounts',
            'is_public' => true
        ]);
    }
}

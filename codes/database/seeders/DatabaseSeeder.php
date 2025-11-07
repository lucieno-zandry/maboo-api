<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Chat;
use App\Models\ChatUser;
use App\Models\Message;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Helpers\Helpers;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(1)->create([
            'password' => Hash::make('00000000'),
            'email' => 'lucieno.zafytody@gmail.com',
            'name' => 'Lucieno',
            'firstname' => 'Zandry',
            'type' => User::TYPE_ADMIN,
        ]);

        User::factory(1)->create([
            'password' => Hash::make('00000000'),
            'email' => 'josulandrie0@gmail.com',
            'name' => 'Lucieno',
            'firstname' => 'Zandry',
            'type' => User::TYPE_CUSTOMER,
            'validated_at' => Helpers::getIsoString(date_create()),
        ]);

        User::factory(10)->create(['type' => User::TYPE_ADMIN]);
        User::factory(10)->create(['type' => User::TYPE_SELLER]);
        User::factory(10)->create(['type' => User::TYPE_CUSTOMER]);

        Category::factory(2)->create();
        Category::factory(4)->create(['parent_id' => 1]);
        Category::factory(4)->create(['parent_id' => 2]);

        for ($i = 3; $i < 11; $i++) {
            Category::factory(4)->create(['parent_id' => $i]);
        }

        Product::factory(200)->create();
        ProductVariant::factory(800)->create();

        for ($i = 1; $i < 21; $i++){
            Chat::factory()->create(['id' => $i]);
            Message::factory()->create(['chat_id' => $i, 'user_id' => $i, 'content' => fake()->text()]);
            ChatUser::factory()->create(['chat_id' => $i, 'user_id' => $i]);

            Message::factory()->create(['chat_id' => $i, 'user_id' => $i + 1, 'content' => fake()->text()]);
            ChatUser::factory()->create(['chat_id' => $i, 'user_id' => $i + 1]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
{
    // 1. إنشاء 5 أقسام
    \App\Models\Category::factory(5)->create();

    // 2. إنشاء مستخدم (إذا لم تكن قد أنشأت واحداً في التينكر)
    \App\Models\User::factory()->create([
        'name' => 'Admin',
        'email' => 'admin@news.com',
        'password' => bcrypt('password'),
    ]);

    // 3. إنشاء 5 بنرات
    \App\Models\Banner::factory(5)->create();

    // 4. إنشاء 20 خبراً
    \App\Models\News::factory(20)->create();
}
}

<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user for testing
        $this->call(AdminUserSeeder::class);

        if (app()->isLocal()) {
            User::factory()->admin()->create();
        }

        User::factory(10)->create();
    }
}

<?php

namespace Database\Seeders;

use App\Models\Checkpoint;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Role::create(['name' => 'super_admin']);
        Role::create(['name' => 'human_resource']);
        Role::create(['name' => 'agent']);

        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@email.com',
        ]);
        $admin->assignRole('super_admin');

        // Create other users
        $users = [
            ['name' => 'Kenji Von Ashley F. Edillo', 'email' => 'kenjivafe@email.com'],
            ['name' => 'Jnmark Friedrich Agustin', 'email' => 'jm@email.com'],
            ['name' => 'Jay-ar Garcia', 'email' => 'jr@email.com'],
            ['name' => 'Sian Meinard Perez', 'email' => 'sian@email.com'],
            ['name' => 'Jam Ramos', 'email' => 'jam@email.com'],
            ['name' => 'Patrick Lopez', 'email' => 'patrick@email.com'],
        ];

        foreach ($users as $userData) {
            $user = User::factory()->create($userData);
            $user->assignRole('agent'); // Assign "agent" role to all users except admin
        }

        $this->call([
            DtrSeeder::class,
            OfficeSeeder::class,
            ProjectSeeder::class,
        ]);

        Checkpoint::create(['name' => 'DICT Region II', 'lat' => 17.62166865586697, 'lng' => 121.72190964112359]);
    }
}

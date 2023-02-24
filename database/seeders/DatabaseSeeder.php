<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Créer les rôles par défaut
        Role::createDefaultRoles();

        DB::table('users')->delete();

        // Créer l'administrateur
        $this->createAdminUser('Admin', 'admin@example.com', 'password');
    }

    private function createAdminUser(string $name, string $email, string $password )
    {
        // Vérifier si un utilisateur "admin" existe déjà
        $adminRole = Role::where('name', 'admin')->first();

        if (!$adminRole) {
            // Créer un rôle "admin" s'il n'existe pas déjà
            $adminRole = Role::create(['name' => 'admin']);
        }

        // Vérifier si un utilisateur avec le même email existe déjà
        $existingUser = User::where('email', $email)->first();

        if (!$existingUser) {
            // Créer l'utilisateur et l'assigner au rôle "admin"
            $user = new User;
            $user->name = $name;
            $user->email = $email;
            $user->password = Hash::make($password);
            $user->role()->associate($adminRole);
            $user->save();
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Memo;
use App\Models\AuditLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create standard users
        $admin = User::create([
            'name' => 'Admin Officer',
            'email' => 'admin@dmams.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $staff = User::create([
            'name' => 'Staff Registrar',
            'email' => 'staff@dmams.com',
            'password' => Hash::make('password'),
            'role' => 'staff',
        ]);

        $viewer = User::create([
            'name' => 'Department Head (Viewer)',
            'email' => 'viewer@dmams.com',
            'password' => Hash::make('password'),
            'role' => 'viewer',
        ]);

        // 2. Clear out any existing memos files in storage if needed, or initialize the directory
        if (!Storage::disk('local')->exists('memos')) {
            Storage::disk('local')->makeDirectory('memos');
        }

        // 3. Log initial system setup audit entry
        AuditLog::create([
            'user_id' => $admin->id,
            'action' => 'login',
            'details' => 'System database initialized with clean seed state.',
            'created_at' => now(),
        ]);
    }
}

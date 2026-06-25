<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role_new')->default('user');
        });

        foreach (DB::table('users')->get() as $user) {
            $newRole = match ($user->role) {
                'super_admin' => 'admin',
                'admin' => 'user',
                default => 'user',
            };

            DB::table('users')->where('id', $user->id)->update(['role_new' => $newRole]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });

        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            DB::statement('ALTER TABLE users RENAME COLUMN role_new TO role');
        } else {
            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('role_new', 'role');
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role_old')->default('admin');
        });

        foreach (DB::table('users')->get() as $user) {
            $oldRole = match ($user->role) {
                'admin' => 'super_admin',
                'user' => 'admin',
                default => 'admin',
            };

            DB::table('users')->where('id', $user->id)->update(['role_old' => $oldRole]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });

        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            DB::statement('ALTER TABLE users RENAME COLUMN role_old TO role');
        } else {
            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('role_old', 'role');
            });
        }
    }
};

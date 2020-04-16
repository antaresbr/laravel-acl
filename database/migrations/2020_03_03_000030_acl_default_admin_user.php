<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AclDefaultAdminUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $now = date('Y-m-d H:i:s');

        DB::table('users')->insert([
            'id' => 1,
            'name' => 'Usuário Admin',
            'username' => 'admin',
            'email' => 'admin@admin.org',
            'email_verified_at' => $now,
            'password' => Hash::make('secret'),
            'created_at' => $now,
            'active' => 1,
            'blocked' => 0,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $user = DB::table('users')->find(1);
        if (!empty($user)) {
            $user->delete();
        }
    }
}

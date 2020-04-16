<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AclAlterUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable(true)->change();

            $table->string('username')->unique()->default('n/a')->after('name');
            $table->unsignedTinyInteger('active')->default(1);
            $table->unsignedTinyInteger('blocked')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();

            $table->dropColumn('username');
            $table->dropColumn('active');
            $table->dropColumn('blocked');
        });
    }
}

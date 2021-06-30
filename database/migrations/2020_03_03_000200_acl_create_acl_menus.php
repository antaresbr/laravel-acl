<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AclCreateAclMenus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acl_menus', function (Blueprint $table) {
            $table->id();
            $table->string('path', 512)->unique();
            $table->string('description');
            $table->tinyInteger('type')->default(0);
            $table->boolean('enabled')->default(true);
            $table->text('style');
            $table->text('action');
            $table->text('component');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('acl_menus');
    }
}

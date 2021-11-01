<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AclCreateAclProfileRights extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acl_profile_rights', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('profile_id');
            $table->unsignedBigInteger('menu_id');
            $table->unsignedTinyInteger('right')->default(0); //-- deny
            $table->boolean('enabled')->default(true);
            $table->timestamps();

            $table->foreign('profile_id')->references('id')->on('acl_profiles')->onUpdate('cascade');
            $table->foreign('menu_id')->references('id')->on('acl_menus')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('acl_profile_rights');
    }
}

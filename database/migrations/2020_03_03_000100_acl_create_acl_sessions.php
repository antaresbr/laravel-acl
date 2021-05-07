<?php

use Antares\Acl\Handlers\AclDbHandler;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AclCreateAclSessions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acl_sessions', function (Blueprint $table) {
            $tsPrecision = config('acl.timestamp_precision');
            $currentTimestamp = AclDbHandler::getCurrentTimestamp();

            $table->id();
            $table->string('api_token', 512)->unique();
            $table->boolean('valid')->default(true);
            $table->unsignedBigInteger('user_id');
            $table->timestamp('issued_at', $tsPrecision)->default(DB::raw($currentTimestamp));
            $table->timestamp('expires_at', $tsPrecision)->default(DB::raw($currentTimestamp));
            $table->timestamp('finished_at', $tsPrecision)->nullable();

            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('acl_sessions');
    }
}

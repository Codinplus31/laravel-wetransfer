<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('upload_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('token', 32)->unique();
            $table->timestamp('expires_at');
            $table->string('email_to_notify')->nullable();
            $table->string('password')->nullable();
            $table->integer('download_count')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('upload_sessions');
    }
};

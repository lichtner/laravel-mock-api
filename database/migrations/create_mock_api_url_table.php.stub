<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('mock_api_url', function (Blueprint $table) {
            $table->id();
            $table->boolean('mock')->default(true);
            $table->dateTime('mock_before')->nullable();
            $table->unsignedInteger('mock_status')->nullable();
            $table->unsignedInteger('last_status');
            $table->string('url', 500);
            $table->timestamps();
            $table->unique('url');
        });
    }

    public function down()
    {
        Schema::dropIfExists('mock_api');
    }
};

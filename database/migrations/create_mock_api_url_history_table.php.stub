<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('mock_api_url_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mock_api_url_id');
            $table->unsignedInteger('status');
            $table->string('content_type');
            $table->mediumText('data');
            $table->timestamp('created_at')->useCurrent();
            $table->index('created_at');
            $table->foreign('mock_api_url_id')
                ->references('id')
                ->on('mock_api_url')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('mock_api_history');
    }
};

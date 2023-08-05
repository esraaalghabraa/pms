<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_batch', function (Blueprint $table) {
            $table->id();
            $table->integer('quantity');
            $table->foreignId('item_id')->constrained('request_items');
            $table->foreignId('batch_id')->constrained('repository_batches');
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
        Schema::dropIfExists('item_batch');
    }
};

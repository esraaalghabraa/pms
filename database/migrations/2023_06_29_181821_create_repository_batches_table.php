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
        Schema::create('repository_batches', function (Blueprint $table) {
            $table->id();
            $table->string('barcode');
            $table->integer('number');
            $table->date('expired_date');
            $table->date('date_of_entry');
            $table->integer('quantity');
            $table->integer('price');
            $table->foreignId('repository_storage_id')->constrained('repository_storages');
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
        Schema::dropIfExists('repository_batches');
    }
};

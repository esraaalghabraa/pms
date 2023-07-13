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
        Schema::create('pharmacy_batches', function (Blueprint $table) {
            $table->id();
            $table->integer('number');
            $table->integer('quantity');
            $table->integer('price');
            $table->bigInteger('barcode');
            $table->date('expired_date');
            $table->foreignId('pharmacy_storage_id')->constrained('pharmacy_storages');
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
        Schema::dropIfExists('pharmacy_batches');
    }
};

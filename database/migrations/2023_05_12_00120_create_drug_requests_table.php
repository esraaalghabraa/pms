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
        Schema::create('drug_requests', function (Blueprint $table) {
            $table->id();
            $table->enum('status',['pending','accepting','rejecting']);
            $table->date('date');
            $table->date('date_delivery')->nullable();
            $table->foreignId('buy_bill_id')->constrained('buy_bills');
            $table->foreignId('repository_id')->constrained('repositories');
            $table->foreignId('pharmacy_id')->constrained('pharmacies');
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
        Schema::dropIfExists('drug_requests');
    }
};

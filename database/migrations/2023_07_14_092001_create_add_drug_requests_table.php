<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Date;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('add_drug_requests', function (Blueprint $table) {
            $table->id();
            $table->string('drug_name');
            $table->string('notes');
            $table->date('date')->default(Date::now());
            $table->enum('status',['pending','accepting','rejecting'])->default('pending');
            $table->string('repository_id')->constrained('repositories');
            $table->softDeletes();
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
        Schema::dropIfExists('add_drug_requests');
    }
};

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
        Schema::create('registration_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name',30)->unique();
            $table->enum('type',['pharmacy','repository']);
            $table->string('document_photo',255)->nullable();
            $table->enum('status',['pending','accepting','rejecting']);
            $table->string('phone_number',30)->unique();
            $table->string('address',30);
            $table->foreignId('owner_id')->constrained('users');
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
        Schema::dropIfExists('registration_requests');
    }
};

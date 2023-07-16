<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id('customer_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('province_id')->nullable();
            $table->unsignedBigInteger('country_id');
            $table->unsignedBigInteger('city_id');
            $table->string('name',35);
            $table->string('contact_name',60);
            $table->string('email')->nullable();
            $table->string('address1',44);
            $table->string('address2',44)->nullable();
            $table->string('zip',12)->nullable();
            $table->string('phone',25)->nullable();
            $table->boolean('is_active')->default(true);
            $table->char('type_customer'); // SENDER or RECEIVER
            $table->json('extra')->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('user_id')
                ->on('users')
                ->onUpdate('cascade');

            $table->foreign('country_id')
                ->references('country_id')
                ->on('countries')
                ->onUpdate('cascade');

            $table->foreign('province_id')
                ->references('province_id')
                ->on('provinces')
                ->onUpdate('cascade');

            $table->foreign('city_id')
                ->references('city_id')
                ->on('cities')
                ->onUpdate('cascade');

        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};

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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id('shipment_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('sender_id');
            $table->unsignedBigInteger('receiver_id');
            $table->char('service_name');  // dhl, fedex, purolator  information
            $table->decimal('price')->default('0.00');
            $table->string('currency',5);
            $table->string('tracking_number', 60);
            $table->json('service');
            $table->json('pieces');
            $table->json('shipment_response');
            $table->json('pickup_request')->nullable();
            $table->json('pickup_response')->nullable();
            $table->json('pickup_cancel')->nullable();
            $table->char('status')->default('outstanding');  // pendiente por pagar ( outstanding ), pagada ( paid ) y  cancelada ( canceled )
            $table->timestamp('s_date');

            $table->timestamps();

            $table->foreign('user_id')
                ->references('user_id')
                ->on('users')
                ->onUpdate('cascade');

            $table->foreign('sender_id')
                ->references('customer_id')
                ->on('customers')
                ->onUpdate('cascade');

            $table->foreign('receiver_id')
                ->references('customer_id')
                ->on('customers')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};

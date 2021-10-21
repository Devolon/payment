<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->string('payment_method');
            $table->decimal('money_amount', 10);
            $table->unsignedBigInteger('user_id')->index();
            $table->json('payment_method_data')->default('[]');
            $table->string("product_type");
            $table->unsignedBigInteger("product_id")->nullable();
            $table->index(["product_type", "product_id"]);
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
        Schema::dropIfExists('transactions');
    }
}

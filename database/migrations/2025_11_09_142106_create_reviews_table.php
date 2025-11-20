<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('rating')->default(5);
            $table->text('comment');
            $table->text('advantages')->nullable();
            $table->text('disadvantages')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->timestamps();

            // Один пользователь может оставить только один отзыв на товар
            $table->unique(['user_id', 'product_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('reviews');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('color')->nullable()->after('price');
            $table->string('material')->nullable()->after('color');
            $table->string('size')->nullable()->after('material');
            $table->string('image')->nullable()->after('size');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['color', 'material', 'size', 'image']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('table_calls', function (Blueprint $row) {
            $row->id();
            $row->foreignId('restaurant_id')->constrained()->onDelete('cascade');
            $row->foreignId('table_id')->constrained()->onDelete('cascade');
            $row->string('status')->default('pending'); // pending, completed
            $row->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('table_calls');
    }
};

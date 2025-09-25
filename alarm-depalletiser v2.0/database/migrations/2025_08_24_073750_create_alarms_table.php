<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('alarms', function (Blueprint $table) {
            $table->id();
            $table->string('description_alarm');
            $table->integer('step');
            $table->timestamps();
        });
        
    }

    public function down(): void
    {
        Schema::dropIfExists('alarms');
    }
};

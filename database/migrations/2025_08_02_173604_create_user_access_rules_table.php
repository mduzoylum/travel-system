<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_access_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('firm_id')->constrained()->onDelete('cascade');
            $table->string('role'); // örn: staff, ceo, manager
            $table->json('rules'); // yıldız, ülke, fiyat kısıtı, vs.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_access_rules');
    }
};

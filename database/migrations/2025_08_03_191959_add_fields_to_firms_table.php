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
        Schema::table('firms', function (Blueprint $table) {
            $table->string('contact_person')->nullable()->after('name');
            $table->string('email')->nullable()->after('contact_person');
            $table->string('phone')->nullable()->after('email');
            $table->text('address')->nullable()->after('phone');
            $table->string('tax_number')->nullable()->after('address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('firms', function (Blueprint $table) {
            $table->dropColumn(['contact_person', 'email', 'phone', 'address', 'tax_number']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_fees', function (Blueprint $table) {
            $table->integer('priority')->default(0)->after('is_mandatory');
        });
    }

    public function down(): void
    {
        Schema::table('service_fees', function (Blueprint $table) {
            $table->dropColumn('priority');
        });
    }
};

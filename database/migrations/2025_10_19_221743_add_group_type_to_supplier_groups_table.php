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
        Schema::table('supplier_groups', function (Blueprint $table) {
            $table->enum('group_type', ['report', 'profit', 'xml', 'manual'])->default('report')->after('name')
                  ->comment('Grup tipi: report=rapor, profit=kar, xml=xml tedarikçi, manual=manuel tedarikçi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_groups', function (Blueprint $table) {
            $table->dropColumn('group_type');
        });
    }
};

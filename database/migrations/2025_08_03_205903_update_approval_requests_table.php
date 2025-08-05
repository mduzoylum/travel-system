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
        Schema::table('approval_requests', function (Blueprint $table) {
            // Eski sütunları kaldır
            $table->dropForeign(['reservation_id']);
            $table->dropColumn(['reservation_id', 'approver_email', 'token', 'responded_at']);
            
            // Yeni sütunları ekle
            $table->unsignedBigInteger('scenario_id')->after('id');
            $table->unsignedBigInteger('requested_by')->after('scenario_id');
            $table->string('request_type')->after('requested_by'); // reservation, modification, cancellation
            $table->json('request_data')->after('request_type');
            $table->timestamp('expires_at')->nullable()->after('status');
            $table->timestamp('approved_at')->nullable()->after('expires_at');
            $table->timestamp('rejected_at')->nullable()->after('approved_at');
            $table->unsignedBigInteger('approved_by')->nullable()->after('rejected_at');
            $table->text('approval_notes')->nullable()->after('approved_by');
            $table->text('rejection_reason')->nullable()->after('approval_notes');
            
            // Foreign key'leri ekle
            $table->foreign('scenario_id')->references('id')->on('approval_scenarios')->onDelete('cascade');
            $table->foreign('requested_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('approval_requests', function (Blueprint $table) {
            // Foreign key'leri kaldır
            $table->dropForeign(['scenario_id', 'requested_by', 'approved_by']);
            
            // Yeni sütunları kaldır
            $table->dropColumn([
                'scenario_id', 'requested_by', 'request_type', 'request_data',
                'expires_at', 'approved_at', 'rejected_at', 'approved_by',
                'approval_notes', 'rejection_reason'
            ]);
            
            // Eski sütunları geri ekle
            $table->foreignId('reservation_id')->constrained()->onDelete('cascade');
            $table->string('approver_email');
            $table->string('token')->unique();
            $table->timestamp('responded_at')->nullable();
        });
    }
};

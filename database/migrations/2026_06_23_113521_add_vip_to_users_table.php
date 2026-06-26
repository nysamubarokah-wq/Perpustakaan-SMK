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
    Schema::table('users', function (Blueprint $table) {
        if (!Schema::hasColumn('users', 'is_vip')) {
            $table->boolean('is_vip')->default(false)->after('email');
        }
        if (!Schema::hasColumn('users', 'vip_expired_at')) {
            $table->timestamp('vip_expired_at')->nullable()->after('is_vip');
        }
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['is_vip', 'vip_expired_at']);
    });
}
};

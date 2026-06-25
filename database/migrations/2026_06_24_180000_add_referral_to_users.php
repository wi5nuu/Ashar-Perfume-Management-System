<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'referral_code')) {
                $table->string('referral_code', 20)->unique()->nullable()->after('role');
            }
            if (!Schema::hasColumn('users', 'referred_by_id')) {
                $table->foreignId('referred_by_id')->nullable()->constrained('users')->nullOnDelete()->after('referral_code');
            }
        });

        // Generate referral codes for existing wholesale_customer users
        $users = DB::table('users')->where('role', 'wholesale_customer')->whereNull('referral_code')->get();
        foreach ($users as $u) {
            $code = strtoupper(Str::random(8));
            while (DB::table('users')->where('referral_code', $code)->exists()) {
                $code = strtoupper(Str::random(8));
            }
            DB::table('users')->where('id', $u->id)->update(['referral_code' => $code]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'referred_by_id')) {
                $table->dropForeign(['referred_by_id']);
                $table->dropColumn('referred_by_id');
            }
            if (Schema::hasColumn('users', 'referral_code')) {
                $table->dropColumn('referral_code');
            }
        });
    }
};

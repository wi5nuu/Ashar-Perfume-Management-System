<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Loyalty credits per customer
        Schema::create('wholesale_credit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->bigInteger('credits')->default(0); // + earned, - spent
            $table->bigInteger('gold_points')->default(0); // + earned (only at top rank)
            $table->string('type'); // 'earn', 'spend', 'gold_earn', 'rank_up'
            $table->string('description')->nullable();
            $table->string('reference_type')->nullable(); // 'order', 'redemption', 'admin'
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->timestamps();
        });

        // Redemption catalog
        Schema::create('wholesale_redemptions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->bigInteger('credits_required'); // e.g. 3000
            $table->string('reward_type'); // 'discount_percent', 'paket_usaha', 'free_shipping', 'product'
            $table->decimal('reward_value', 15, 2)->nullable(); // e.g. 5 (% discount), or amount
            $table->json('meta')->nullable(); // extra config
            $table->boolean('is_active')->default(true);
            $table->integer('max_uses_per_customer')->default(0); // 0 = unlimited
            $table->timestamps();
        });

        // Customer redemptions
        Schema::create('wholesale_customer_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('redemption_id')->constrained('wholesale_redemptions');
            $table->bigInteger('credits_spent');
            $table->string('status')->default('pending'); // pending, used, expired
            $table->timestamp('used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        // Add loyalty rank & totals to customers table
        Schema::table('customers', function (Blueprint $table) {
            $table->string('loyalty_rank')->default('Regular');
            $table->bigInteger('total_credits_earned')->default(0);
            $table->bigInteger('total_credits_spent')->default(0);
            $table->bigInteger('gold_points')->default(0);
            $table->decimal('lifetime_spend', 15, 2)->default(0);
        });
    }

    public function down()
    {
        Schema::dropIfExists('wholesale_customer_redemptions');
        Schema::dropIfExists('wholesale_redemptions');
        Schema::dropIfExists('wholesale_credit_logs');
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['loyalty_rank', 'total_credits_earned', 'total_credits_spent', 'gold_points', 'lifetime_spend']);
        });
    }
};

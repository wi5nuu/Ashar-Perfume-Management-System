<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── UNIQUE CONSTRAINT: products.barcode ───────────────────────────
        Schema::table('products', function (Blueprint $table) {
            $table->string('barcode')->nullable()->change();
        });
        try {
            Schema::table('products', function (Blueprint $table) {
                $table->unique('barcode', 'products_barcode_unique');
            });
        } catch (\Exception $e) {
            // May already exist
        }

        // ─── FK: shifts.reviewed_by → users.id ────────────────────────────
        try {
            Schema::table('shifts', function (Blueprint $table) {
                $table->foreign('reviewed_by')->references('id')->on('users')->nullOnDelete();
            });
        } catch (\Exception $e) {
            // May already exist
        }

        // ─── FK: sales_returns.approved_by → users.id ──────────────────────
        try {
            Schema::table('sales_returns', function (Blueprint $table) {
                $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
            });
        } catch (\Exception $e) {
            // May already exist
        }

        // ─── CASCADE → RESTRICT: goods_receipts.product_id ─────────────────
        $this->safeChangeForeignKey('goods_receipts', 'product_id', 'products', 'restrict');

        // ─── CASCADE → RESTRICT: goods_receipts.branch_id ──────────────────
        $this->safeChangeForeignKey('goods_receipts', 'branch_id', 'branches', 'restrict');

        // ─── ADDITIONAL INDEXES for performance ────────────────────────────
        $this->safeCreateIndex('goods_receipts', 'idx_gr_received_date', ['received_date']);
        $this->safeCreateIndex('goods_receipts', 'idx_gr_recorded_by', ['recorded_by']);
        $this->safeCreateIndex('stock_requests', 'idx_sr_status', ['status']);
        $this->safeCreateIndex('stock_requests', 'idx_sr_branch_status', ['branch_id', 'status']);
        $this->safeCreateIndex('shifts', 'idx_shift_status', ['status']);
        $this->safeCreateIndex('shifts', 'idx_shift_user_status', ['user_id', 'status']);
        $this->safeCreateIndex('sales_returns', 'idx_sr_status', ['status']);
        $this->safeCreateIndex('sales_returns', 'idx_sr_branch', ['branch_id']);
        $this->safeCreateIndex('login_activities', 'idx_la_created_at', ['created_at']);
        $this->safeCreateIndex('login_activities', 'idx_la_user_id', ['user_id']);
        $this->safeCreateIndex('audit_logs', 'idx_al_created_at', ['created_at']);
        $this->safeCreateIndex('audit_logs', 'idx_al_user_id', ['user_id']);
        $this->safeCreateIndex('notifications', 'idx_notif_read_at', ['read_at']);
        $this->safeCreateIndex('commission', 'idx_comm_month', ['month']);
        $this->safeCreateIndex('commission', 'idx_comm_user_month', ['user_id', 'month']);
        $this->safeCreateIndex('commission', 'idx_comm_status', ['status']);
        $this->safeCreateIndex('coupons', 'idx_coupon_status', ['status']);
        $this->safeCreateIndex('coupons', 'idx_coupon_customer', ['customer_id']);
        $this->safeCreateIndex('debt_payments', 'idx_dp_customer', ['customer_id']);
    }

    private function safeCreateIndex(string $table, string $name, array $columns): void
    {
        try {
            Schema::table($table, function (Blueprint $t) use ($name, $columns) {
                $t->index($columns, $name);
            });
        } catch (\Exception $e) {
        }
    }

    private function safeChangeForeignKey(string $table, string $column, string $references, string $onDelete): void
    {
        try {
            Schema::table($table, function (Blueprint $t) use ($column) {
                $t->dropForeign([$column]);
            });
        } catch (\Exception $e) {
            // FK may not exist
        }

        try {
            Schema::table($table, function (Blueprint $t) use ($column, $references, $onDelete) {
                $t->foreign($column)->references('id')->on($references)->onDelete($onDelete);
            });
        } catch (\Exception $e) {
            // May already have the desired constraint
        }
    }

    public function down(): void
    {
        try {
            Schema::table('products', function (Blueprint $table) {
                $table->dropUnique('products_barcode_unique');
            });
        } catch (\Exception $e) {
        }

        try {
            Schema::table('shifts', function (Blueprint $table) {
                $table->dropForeign(['reviewed_by']);
            });
        } catch (\Exception $e) {
        }

        try {
            Schema::table('sales_returns', function (Blueprint $table) {
                $table->dropForeign(['approved_by']);
            });
        } catch (\Exception $e) {
        }

        $this->safeChangeForeignKey('goods_receipts', 'product_id', 'products', 'cascade');
        $this->safeChangeForeignKey('goods_receipts', 'branch_id', 'branches', 'cascade');

        $indexes = [
            'goods_receipts' => ['idx_gr_received_date', 'idx_gr_recorded_by'],
            'stock_requests' => ['idx_sr_status', 'idx_sr_branch_status'],
            'shifts' => ['idx_shift_status', 'idx_shift_user_status'],
            'sales_returns' => ['idx_sr_status', 'idx_sr_branch'],
            'login_activities' => ['idx_la_created_at', 'idx_la_user_id'],
            'audit_logs' => ['idx_al_created_at', 'idx_al_user_id'],
            'notifications' => ['idx_notif_read_at'],
            'commission' => ['idx_comm_month', 'idx_comm_user_month', 'idx_comm_status'],
            'coupons' => ['idx_coupon_status', 'idx_coupon_customer'],
            'debt_payments' => ['idx_dp_customer'],
        ];

        foreach ($indexes as $table => $names) {
            try {
                Schema::table($table, function (Blueprint $t) use ($names) {
                    foreach ($names as $idx) {
                        if (Schema::hasIndex($t->getTable(), $idx)) {
                            $t->dropIndex($idx);
                        }
                    }
                });
            } catch (\Exception $e) {
            }
        }
    }
};

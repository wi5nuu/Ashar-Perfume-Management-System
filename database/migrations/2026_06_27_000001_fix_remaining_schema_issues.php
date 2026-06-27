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
        Schema::table('goods_receipts', function (Blueprint $table) {
            if (!Schema::hasIndex('goods_receipts', 'idx_gr_received_date')) {
                $table->index('received_date', 'idx_gr_received_date');
            }
            if (!Schema::hasIndex('goods_receipts', 'idx_gr_recorded_by')) {
                $table->index('recorded_by', 'idx_gr_recorded_by');
            }
        });

        Schema::table('stock_requests', function (Blueprint $table) {
            if (!Schema::hasIndex('stock_requests', 'idx_sr_status')) {
                $table->index('status', 'idx_sr_status');
            }
            if (!Schema::hasIndex('stock_requests', 'idx_sr_branch_status')) {
                $table->index(['branch_id', 'status'], 'idx_sr_branch_status');
            }
        });

        Schema::table('shifts', function (Blueprint $table) {
            if (!Schema::hasIndex('shifts', 'idx_shift_status')) {
                $table->index('status', 'idx_shift_status');
            }
            if (!Schema::hasIndex('shifts', 'idx_shift_user_status')) {
                $table->index(['user_id', 'status'], 'idx_shift_user_status');
            }
        });

        Schema::table('sales_returns', function (Blueprint $table) {
            if (!Schema::hasIndex('sales_returns', 'idx_sr_status')) {
                $table->index('status', 'idx_sr_status');
            }
            if (!Schema::hasIndex('sales_returns', 'idx_sr_branch')) {
                $table->index('branch_id', 'idx_sr_branch');
            }
        });

        Schema::table('login_activities', function (Blueprint $table) {
            if (!Schema::hasIndex('login_activities', 'idx_la_created_at')) {
                $table->index('created_at', 'idx_la_created_at');
            }
            if (!Schema::hasIndex('login_activities', 'idx_la_user_id')) {
                $table->index('user_id', 'idx_la_user_id');
            }
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            if (!Schema::hasIndex('audit_logs', 'idx_al_created_at')) {
                $table->index('created_at', 'idx_al_created_at');
            }
            if (!Schema::hasIndex('audit_logs', 'idx_al_user_id')) {
                $table->index('user_id', 'idx_al_user_id');
            }
        });

        Schema::table('notifications', function (Blueprint $table) {
            if (!Schema::hasIndex('notifications', 'idx_notif_read_at')) {
                $table->index('read_at', 'idx_notif_read_at');
            }
        });

        Schema::table('commission', function (Blueprint $table) {
            if (!Schema::hasIndex('commission', 'idx_comm_month')) {
                $table->index('month', 'idx_comm_month');
            }
            if (!Schema::hasIndex('commission', 'idx_comm_user_month')) {
                $table->index(['user_id', 'month'], 'idx_comm_user_month');
            }
            if (!Schema::hasIndex('commission', 'idx_comm_status')) {
                $table->index('status', 'idx_comm_status');
            }
        });

        Schema::table('coupons', function (Blueprint $table) {
            if (!Schema::hasIndex('coupons', 'idx_coupon_status')) {
                $table->index('status', 'idx_coupon_status');
            }
            if (!Schema::hasIndex('coupons', 'idx_coupon_customer')) {
                $table->index('customer_id', 'idx_coupon_customer');
            }
        });

        Schema::table('debt_payments', function (Blueprint $table) {
            if (!Schema::hasIndex('debt_payments', 'idx_dp_customer')) {
                $table->index('customer_id', 'idx_dp_customer');
            }
        });
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

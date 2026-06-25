<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Phase 2 migration: Soft deletes, cascade fixes, unique constraints, audit columns.
     * All operations are idempotent — safe to re-run.
     */
    public function up(): void
    {
        // ─── SOFT DELETES for financial tables ────────────────────────────
        $softDeleteTables = [
            'transactions',
            'expenses',
            'wholesale_orders',
            'debt_payments',
        ];

        foreach ($softDeleteTables as $table) {
            if (!Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->softDeletes();
                });
            }
        }

        // ─── UNIQUE CONSTRAINT: inventories(product_id, branch_id) ────────
        if (!Schema::hasIndex('inventories', 'inv_product_branch_unique')) {
            Schema::table('inventories', function (Blueprint $table) {
                // Remove duplicate rows first (keep the one with highest id)
                $duplicates = DB::select("
                    SELECT product_id, branch_id, MAX(id) AS keep_id
                    FROM inventories
                    GROUP BY product_id, branch_id
                    HAVING COUNT(*) > 1
                ");

                foreach ($duplicates as $dup) {
                    DB::table('inventories')
                        ->where('product_id', $dup->product_id)
                        ->where('branch_id', $dup->branch_id)
                        ->where('id', '!=', $dup->keep_id)
                        ->delete();
                }

                $table->unique(['product_id', 'branch_id'], 'inv_product_branch_unique');
            });
        }

        // ─── CASCADE DELETE → RESTRICT for products FK ────────────────────
        $this->changeForeignKey(
            'products',
            'product_category_id',
            'product_categories',
            'restrict'
        );

        $this->changeForeignKey(
            'inventories',
            'product_id',
            'products',
            'restrict'
        );

        // ─── AUDIT COLUMNS for key financial tables ───────────────────────
        $auditTables = ['transactions', 'expenses', 'wholesale_orders'];

        foreach ($auditTables as $table) {
            Schema::table($table, function (Blueprint $t) {
                if (!Schema::hasColumn($t->getTable(), 'updated_by')) {
                    $t->unsignedBigInteger('updated_by')->nullable()->after('user_id');
                }
                if (!Schema::hasColumn($t->getTable(), 'deleted_by')) {
                    $t->unsignedBigInteger('deleted_by')->nullable()->after('updated_by');
                }
            });
        }

        // ─── ADD INDEXES for commonly queried columns ─────────────────────
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasIndex('transactions', 'transactions_invoice_number_unique')) {
                $table->unique('invoice_number');
            }
            if (!Schema::hasIndex('transactions', 'idx_payment_status')) {
                $table->index('payment_status', 'idx_payment_status');
            }
            if (!Schema::hasIndex('transactions', 'idx_user_id')) {
                $table->index('user_id', 'idx_user_id');
            }
        });

        Schema::table('expenses', function (Blueprint $table) {
            if (!Schema::hasIndex('expenses', 'idx_expense_branch')) {
                $table->index('branch_id', 'idx_expense_branch');
            }
            if (!Schema::hasIndex('expenses', 'idx_expense_date')) {
                $table->index('date', 'idx_expense_date');
            }
        });

        Schema::table('debt_payments', function (Blueprint $table) {
            if (!Schema::hasIndex('debt_payments', 'idx_dp_transaction')) {
                $table->index('transaction_id', 'idx_dp_transaction');
            }
            if (!Schema::hasIndex('debt_payments', 'idx_dp_date')) {
                $table->index('payment_date', 'idx_dp_date');
            }
        });
    }

    /**
     * Safely change a foreign key's onDelete action.
     * MySQL doesn't support ALTER FOREIGN KEY — must drop and recreate.
     */
    private function changeForeignKey(
        string $table,
        string $column,
        string $referencesTable,
        string $onDelete
    ): void {
        try {
            Schema::table($table, function (Blueprint $t) use ($column) {
                $t->dropForeign([$column]);
            });
        } catch (\Exception $e) {
            // FK might not exist — safe to ignore
        }

        try {
            Schema::table($table, function (Blueprint $t) use ($column, $referencesTable, $onDelete) {
                $t->foreign($column)
                    ->references('id')
                    ->on($referencesTable)
                    ->onDelete($onDelete);
            });
        } catch (\Exception $e) {
            // FK already has the desired constraint — safe to ignore
        }
    }

    public function down(): void
    {
        foreach (['debt_payments', 'wholesale_orders', 'expenses', 'transactions'] as $table) {
            if (Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropSoftDeletes();
                });
            }
        }

        if (Schema::hasIndex('inventories', 'inv_product_branch_unique')) {
            Schema::table('inventories', function (Blueprint $table) {
                $table->dropUnique('inv_product_branch_unique');
            });
        }

        // Restore cascade deletes
        $this->changeForeignKey('products', 'product_category_id', 'product_categories', 'cascade');
        $this->changeForeignKey('inventories', 'product_id', 'products', 'cascade');

        foreach (['wholesale_orders', 'expenses', 'transactions'] as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropColumn(['updated_by', 'deleted_by']);
            });
        }

        // Remove added indexes
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasIndex('transactions', 'transactions_invoice_number_unique')) {
                $table->dropUnique(['invoice_number']);
            }
            if (Schema::hasIndex('transactions', 'idx_payment_status')) {
                $table->dropIndex('idx_payment_status');
            }
            if (Schema::hasIndex('transactions', 'idx_user_id')) {
                $table->dropIndex('idx_user_id');
            }
        });

        Schema::table('expenses', function (Blueprint $table) {
            if (Schema::hasIndex('expenses', 'idx_expense_branch')) {
                $table->dropIndex('idx_expense_branch');
            }
            if (Schema::hasIndex('expenses', 'idx_expense_date')) {
                $table->dropIndex('idx_expense_date');
            }
        });

        Schema::table('debt_payments', function (Blueprint $table) {
            if (Schema::hasIndex('debt_payments', 'idx_dp_transaction')) {
                $table->dropIndex('idx_dp_transaction');
            }
            if (Schema::hasIndex('debt_payments', 'idx_dp_date')) {
                $table->dropIndex('idx_dp_date');
            }
        });
    }
};

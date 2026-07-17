<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'allow_sell_without_stock')) {
                $table->boolean('allow_sell_without_stock')->default(false)->after('reduced_igv_percent');
            }
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'allow_sell_without_stock')) {
                $table->dropColumn('allow_sell_without_stock');
            }
        });
    }
};

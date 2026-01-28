<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bill_types', function (Blueprint $table) {
            $table->boolean('is_flexible')->default(false)->after('amount')
                  ->comment('Jika true, nominal bisa diisi sesuai kemampuan');
        });
    }

    public function down(): void
    {
        Schema::table('bill_types', function (Blueprint $table) {
            $table->dropColumn('is_flexible');
        });
    }
};

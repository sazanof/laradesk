<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->integer('department_id')->after('category_id');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->integer('department_id')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('department_id');
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('department_id');
        });
    }
};

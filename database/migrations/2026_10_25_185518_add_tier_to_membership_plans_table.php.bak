
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('membership_plans', function (Blueprint $table) {
            $table->unsignedInteger('tier')->default(1);
        });
    }

    public function down(): void
    {
        Schema::table('membership_plans', function (Blueprint $table) {
            $table->dropColumn('tier');
        });
    }
};

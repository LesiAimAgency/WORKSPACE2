<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('materials')) {
            Schema::table('materials', function (Blueprint $table) {
                if (! Schema::hasColumn('materials', 'suitable_typologies')) {
                    $table->json('suitable_typologies')->nullable()->after('application');
                }
                if (! Schema::hasColumn('materials', 'suitable_areas')) {
                    $table->json('suitable_areas')->nullable()->after('suitable_locations');
                }
            });

            // Backfill existing materials with default typologies so Admin sees checked options immediately
            $defaultTypologies = ['Office', 'School', 'Townhouse', 'Coffee Shop', 'Retail', 'Apartment'];
            $materials = DB::table('materials')->get();
            foreach ($materials as $m) {
                $updates = [];
                if (empty($m->suitable_typologies)) {
                    $updates['suitable_typologies'] = json_encode($defaultTypologies);
                }
                if (! empty($updates)) {
                    DB::table('materials')->where('id', $m->id)->update($updates);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('materials')) {
            Schema::table('materials', function (Blueprint $table) {
                $cols = [];
                if (Schema::hasColumn('materials', 'suitable_typologies')) {
                    $cols[] = 'suitable_typologies';
                }
                if (Schema::hasColumn('materials', 'suitable_areas')) {
                    $cols[] = 'suitable_areas';
                }
                if (! empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }
    }
};

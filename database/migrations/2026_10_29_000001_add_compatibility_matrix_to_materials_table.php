<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\MaterialController;
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
                if (! Schema::hasColumn('materials', 'compatibility_matrix')) {
                    $table->json('compatibility_matrix')->nullable()->after('suitable_areas');
                }
            });

            // Backfill existing materials with 3-tier compatibility structure
            $typologies = MaterialController::getDefaultTypologies();
            $surfaces = MaterialController::getDefaultSurfaces();
            $areaMatrix = MaterialController::getDefaultAreaMatrix();

            $typoKeys = array_map(fn ($t) => $t['en'], $typologies);
            $surfKeys = array_map(fn ($s) => $s['en'], $surfaces);

            $materials = DB::table('materials')->get();

            foreach ($materials as $m) {
                $rawTypo = ! empty($m->suitable_typologies) ? json_decode($m->suitable_typologies, true) : [];
                $rawSurf = ! empty($m->suitable_locations) ? json_decode($m->suitable_locations, true) : [];
                $rawAreas = ! empty($m->suitable_areas) ? json_decode($m->suitable_areas, true) : [];

                if (! is_array($rawTypo) || empty($rawTypo)) {
                    $rawTypo = $typoKeys;
                }
                if (! is_array($rawSurf)) {
                    $rawSurf = [];
                }
                if (! is_array($rawAreas)) {
                    $rawAreas = [];
                }

                $cleanAreas = array_map(fn ($a) => MaterialController::canonicalizeArea((string) $a), $rawAreas);

                $compat = [];
                foreach ($typoKeys as $tKey) {
                    $isTypoEnabled = in_array($tKey, $rawTypo, true);
                    $compat[$tKey] = [
                        'enabled' => $isTypoEnabled,
                        'surfaces' => [],
                    ];

                    foreach ($surfKeys as $sKey) {
                        $isSurfEnabled = in_array($sKey, $rawSurf, true);
                        $defaultPairAreas = $areaMatrix[$tKey][$sKey] ?? [];

                        // If the material has specific areas selected, intersect with pair areas
                        $pairSelectedAreas = [];
                        if (! empty($cleanAreas)) {
                            $pairSelectedAreas = array_values(array_intersect($cleanAreas, $defaultPairAreas));
                        }

                        $compat[$tKey]['surfaces'][$sKey] = [
                            'enabled' => $isSurfEnabled,
                            'areas' => $pairSelectedAreas, // Empty array = applies to ALL areas of this surface
                        ];
                    }
                }

                DB::table('materials')->where('id', $m->id)->update([
                    'compatibility_matrix' => json_encode($compat, JSON_UNESCAPED_UNICODE),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('materials') && Schema::hasColumn('materials', 'compatibility_matrix')) {
            Schema::table('materials', function (Blueprint $table) {
                $table->dropColumn('compatibility_matrix');
            });
        }
    }
};


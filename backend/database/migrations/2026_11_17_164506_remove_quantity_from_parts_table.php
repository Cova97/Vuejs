<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * "quantity" deja de guardarse en parts: ahora se calcula como la suma
     * del stock de la pieza en todas las empresas (tabla company_part).
     */
    public function up(): void
    {
        // 1) Antes de borrar la columna, guardamos las cantidades actuales
        //    para no perderlas: las pasamos como stock de una empresa "Bodega principal".
        $parts = DB::table('parts')->where('quantity', '>', 0)->get(['id', 'quantity']);

        if ($parts->isNotEmpty()) {
            $now = now();

            $bodegaId = DB::table('companies')->where('rfc', 'BODEGA0000000')->value('id')
                ?? DB::table('companies')->insertGetId([
                    'name' => 'Bodega principal',
                    'rfc' => 'BODEGA0000000',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

            foreach ($parts as $part) {
                // insertOrIgnore: si esa pieza ya estaba en la bodega, no la duplica.
                DB::table('company_part')->insertOrIgnore([
                    'company_id' => $bodegaId,
                    'part_id' => $part->id,
                    'stock' => $part->quantity,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // 2) Ahora sí, borramos la columna.
        Schema::table('parts', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Deshacer: volver a crear la columna igual que en la migración original...
        Schema::table('parts', function (Blueprint $table) {
            $table->unsignedInteger('quantity')->default(0)->after('description');
        });

        // ...y llenarla con la suma del stock de todas las empresas.
        DB::table('parts')->update([
            'quantity' => DB::raw('(select coalesce(sum(stock), 0) from company_part where company_part.part_id = parts.id)'),
        ]);
    }
};

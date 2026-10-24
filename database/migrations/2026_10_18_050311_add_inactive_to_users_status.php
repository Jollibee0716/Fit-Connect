<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $col = DB::selectOne("SHOW COLUMNS FROM users LIKE 'status'");

        if (!$col || stripos($col->Type, 'enum(') !== 0) {
            return;
        }

        preg_match_all("/'((?:[^']|'')*)'/", $col->Type, $m);
        $values = $m[1];

        if (in_array('inactive', $values, true)) {
            return;
        }

        $values[] = 'inactive';
        $list    = "'" . implode("','", $values) . "'";
        $null    = $col->Null === 'YES' ? 'NULL' : 'NOT NULL';
        $default = $col->Default !== null
            ? "DEFAULT '" . str_replace("'", "''", $col->Default) . "'"
            : '';

        DB::statement("ALTER TABLE users MODIFY status ENUM($list) $null $default");
    }

    public function down(): void
    {
        // Left empty on purpose.
    }
};
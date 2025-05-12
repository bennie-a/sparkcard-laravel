<?php
namespace Tests\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TruncateAllTables extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        logger()->info("全テーブル削除開始");
        $tableNames = DB::getDoctrineSchemaManager()->listTableNames();
        Schema::disableForeignKeyConstraints();
        $excludes = ["migrations", "pgsodium.key", "realtime.subscription"];
        foreach($tableNames as $name) {
            if (in_array($name, $excludes)) {
                continue;
            }
            DB::table($name)->truncate();
        }
        Schema::enableForeignKeyConstraints();
        logger()->info("全テーブル削除終了");
    }

    /**
     * @return array
     */
    private function getTargetTableNames(): array
    {
        $excludes = ['migrations'];
        return array_diff($this->getAllTableNames(), $excludes);
    }

    /**
     * @return array
     */
    private function getAllTableNames(): array
    {
        return DB::connection()->getDoctrineSchemaManager()->listTableNames();
    }
}

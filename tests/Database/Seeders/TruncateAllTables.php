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
        logger()->info("全テーブルtruncate開始");
        $databaseName = DB::getDatabaseName();
        $tableNames = DB::table('pg_catalog.pg_tables')
            ->where('schemaname', 'public')
            ->pluck('tablename')
            ->toArray();

        // 除外テーブル
        $excludes = ['migrations', 'pgsodium.key', 'realtime.subscription'];

        Schema::disableForeignKeyConstraints();

        foreach ($tableNames as $name) {
            if (in_array($name, $excludes)) {
                continue;
            }
            DB::table($name)->truncate();
        }

        Schema::enableForeignKeyConstraints();
        logger()->info("全テーブルtruncate終了");
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

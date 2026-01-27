<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class FreshMigrateOracle extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'migrate:fresh-oracle {--seed : Seed the database after migration} {--force : Force the operation to run when in production}';

    /**
     * The console command description.
     */
    protected $description = 'Drop all tables and re-run migrations for Oracle (fixes IDENTITY sequence errors)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (! $this->option('force') && ! $this->confirm('⚠️  This will DROP ALL TABLES in the database. Continue?', false)) {
            $this->info('Operation cancelled.');
            return 0;
        }

        try {
            $this->info('🗑️  Dropping all tables and sequences...');

            // Usar un enfoque seguro para Oracle que excluye secuencias IDENTITY
            DB::statement("
                BEGIN
                    -- Eliminar todas las tablas
                    FOR c IN (SELECT table_name FROM user_tables WHERE secondary = 'N') LOOP
                        EXECUTE IMMEDIATE ('DROP TABLE \"' || c.table_name || '\" CASCADE CONSTRAINTS');
                    END LOOP;
                    
                    -- Solo eliminar secuencias que NO sean generadas por IDENTITY
                    FOR s IN (
                        SELECT sequence_name 
                        FROM user_sequences 
                        WHERE sequence_name NOT IN (
                            SELECT COALESCE(sequence_name, 'NULL') 
                            FROM user_tab_identity_cols
                            WHERE sequence_name IS NOT NULL
                        )
                    ) LOOP
                        EXECUTE IMMEDIATE ('DROP SEQUENCE ' || s.sequence_name);
                    END LOOP;
                END;
            ");

            $this->info('✅ All tables dropped successfully');

            $this->newLine();
            $this->info('🔄 Running migrations...');

            $exitCode = Artisan::call('migrate', [], $this->getOutput());

            if ($exitCode !== 0) {
                $this->error('❌ Migrations failed');
                return $exitCode;
            }

            $this->info('✅ Migrations completed successfully');

            if ($this->option('seed')) {
                $this->newLine();
                $this->info('🌱 Seeding database...');

                $exitCode = Artisan::call('db:seed', [], $this->getOutput());

                if ($exitCode !== 0) {
                    $this->error('❌ Seeding failed');
                    return $exitCode;
                }

                $this->info('✅ Seeding completed successfully');
            }

            $this->newLine();
            $this->info('🎉 Fresh migration completed!');

            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }
    }
}

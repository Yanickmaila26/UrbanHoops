<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Yajra\Oci8\Schema\OracleBuilder;

class OracleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Solo aplicar para conexión Oracle
        if (config('database.default') === 'oracle') {
            // Override del método dropAllTables para evitar error con secuencias IDENTITY
            DB::getSchemaBuilder()->macro('dropAllTablesFixed', function () {
                DB::statement("
                    BEGIN
                        FOR c IN (SELECT table_name FROM user_tables WHERE secondary = 'N') LOOP
                            EXECUTE IMMEDIATE ('DROP TABLE \"' || c.table_name || '\" CASCADE CONSTRAINTS');
                        END LOOP;

                        -- Solo borrar secuencias que NO sean generadas por IDENTITY
                        FOR s IN (
                            SELECT sequence_name 
                            FROM user_sequences 
                            WHERE sequence_name NOT IN (
                                SELECT sequence_name 
                                FROM user_tab_identity_cols
                            )
                        ) LOOP
                            EXECUTE IMMEDIATE ('DROP SEQUENCE ' || s.sequence_name);
                        END LOOP;
                    END;
                ");
            });
        }
    }
}

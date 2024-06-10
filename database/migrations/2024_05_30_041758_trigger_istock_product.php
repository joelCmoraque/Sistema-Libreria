<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Función para el trigger de inserción en 'inputs'
        DB::unprepared('
            CREATE OR REPLACE FUNCTION update_stock_after_insert() 
            RETURNS TRIGGER AS $$
            BEGIN
                UPDATE products
                SET stock_actual = stock_actual + NEW.cantidad
                WHERE id = NEW.product_id;
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ');

        // Crear el trigger que llama a la función después de insertar en 'inputs'
        DB::unprepared('
            CREATE TRIGGER trigger_istock_product
            AFTER INSERT ON inputs
            FOR EACH ROW
            EXECUTE FUNCTION update_stock_after_insert();
        ');

        //Función para el trigger de inserción en 'outputs'
        DB::unprepared('
            CREATE OR REPLACE FUNCTION update_stock_after_output_insert() 
            RETURNS TRIGGER AS $$
            BEGIN
                -- Verificar si hay suficiente stock antes de actualizar
                IF (SELECT stock_actual >= NEW.cantidad FROM products WHERE id = NEW.product_id) THEN
                    -- Actualizar el stock
                    UPDATE products
                    SET stock_actual = stock_actual - NEW.cantidad
                    WHERE id = NEW.product_id;
                END IF;
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ');
        
        // Crear el trigger que llama a la función después de insertar en 'outputs'
        DB::unprepared('
            CREATE TRIGGER trigger_dstock_product
            AFTER INSERT ON outputs
            FOR EACH ROW
            EXECUTE FUNCTION update_stock_after_output_insert();
        ');
        

        // Función para el trigger de actualización en 'inputs'
        DB::unprepared('
            CREATE OR REPLACE FUNCTION update_stock_after_input_update() 
            RETURNS TRIGGER AS $$
            BEGIN
                UPDATE products
                SET stock_actual = stock_actual - OLD.cantidad + NEW.cantidad
                WHERE id = NEW.product_id;
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ');

        // Crear el trigger que llama a la función después de actualizar en 'inputs'
        DB::unprepared('
            CREATE TRIGGER trigger_update_istock_product
            AFTER UPDATE ON inputs
            FOR EACH ROW
            EXECUTE FUNCTION update_stock_after_input_update();
        ');

        // Función para el trigger de actualización en 'outputs'
        DB::unprepared('
            CREATE OR REPLACE FUNCTION update_stock_after_output_update() 
            RETURNS TRIGGER AS $$
            BEGIN
                UPDATE products
                SET stock_actual = stock_actual + OLD.cantidad - NEW.cantidad
                WHERE id = NEW.product_id;
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ');

        // Crear el trigger que llama a la función después de actualizar en 'outputs'
        DB::unprepared('
            CREATE TRIGGER trigger_update_dstock_product
            AFTER UPDATE ON outputs
            FOR EACH ROW
            EXECUTE FUNCTION update_stock_after_output_update();
        ');

        // Función para el trigger de eliminación en 'inputs'
        DB::unprepared('
            CREATE OR REPLACE FUNCTION update_stock_after_input_delete() 
            RETURNS TRIGGER AS $$
            BEGIN
                UPDATE products
                SET stock_actual = stock_actual - OLD.cantidad
                WHERE id = OLD.product_id;
                RETURN OLD;
            END;
            $$ LANGUAGE plpgsql;
        ');

        // Crear el trigger que llama a la función después de eliminar en 'inputs'
        DB::unprepared('
            CREATE TRIGGER trigger_delete_istock_product
            AFTER DELETE ON inputs
            FOR EACH ROW
            EXECUTE FUNCTION update_stock_after_input_delete();
        ');

        // Función para el trigger de eliminación en 'outputs'
        DB::unprepared('
            CREATE OR REPLACE FUNCTION update_stock_after_output_delete() 
            RETURNS TRIGGER AS $$
            BEGIN
                UPDATE products
                SET stock_actual = stock_actual + OLD.cantidad
                WHERE id = OLD.product_id;
                RETURN OLD;
            END;
            $$ LANGUAGE plpgsql;
        ');

        // Crear el trigger que llama a la función después de eliminar en 'outputs'
        DB::unprepared('
            CREATE TRIGGER trigger_delete_dstock_product
            AFTER DELETE ON outputs
            FOR EACH ROW
            EXECUTE FUNCTION update_stock_after_output_delete();
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar los triggers y funciones en caso de rollback
        DB::unprepared('DROP TRIGGER IF EXISTS trigger_istock_product ON inputs;');
        DB::unprepared('DROP FUNCTION IF EXISTS update_stock_after_insert();');
        
        DB::unprepared('DROP TRIGGER IF EXISTS trigger_dstock_product ON outputs;');
        DB::unprepared('DROP FUNCTION IF EXISTS update_stock_after_output_insert();');

        DB::unprepared('DROP TRIGGER IF EXISTS trigger_update_istock_product ON inputs;');
        DB::unprepared('DROP FUNCTION IF EXISTS update_stock_after_input_update();');
        
        DB::unprepared('DROP TRIGGER IF EXISTS trigger_update_dstock_product ON outputs;');
        DB::unprepared('DROP FUNCTION IF EXISTS update_stock_after_output_update();');

        DB::unprepared('DROP TRIGGER IF EXISTS trigger_delete_istock_product ON inputs;');
        DB::unprepared('DROP FUNCTION IF EXISTS update_stock_after_input_delete();');
        
        DB::unprepared('DROP TRIGGER IF EXISTS trigger_delete_dstock_product ON outputs;');
        DB::unprepared('DROP FUNCTION IF EXISTS update_stock_after_output_delete();');
    }
};

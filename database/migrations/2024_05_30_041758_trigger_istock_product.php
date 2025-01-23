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


 // Crear la función para el trigger de inserción
 DB::unprepared('
 CREATE OR REPLACE FUNCTION set_cantidad_restante()
 RETURNS TRIGGER AS $$
 BEGIN
     NEW.cantidad_restante = NEW.cantidad;
     RETURN NEW;
 END;
 $$ LANGUAGE plpgsql;
');

// Crear el trigger que llama a la función antes de insertar en 'inputs'
DB::unprepared('
 CREATE TRIGGER trigger_set_cantidad_restante
 BEFORE INSERT ON inputs
 FOR EACH ROW
 EXECUTE FUNCTION set_cantidad_restante();
');

DB::unprepared('
CREATE OR REPLACE FUNCTION update_cantidad_restante()
RETURNS TRIGGER AS $$
BEGIN
    NEW.cantidad_restante = NEW.cantidad;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;
');

DB::unprepared('
CREATE TRIGGER trigger_update_cantidad_restante
BEFORE UPDATE OF cantidad ON inputs
FOR EACH ROW
EXECUTE FUNCTION update_cantidad_restante();
');


DB::unprepared('
CREATE OR REPLACE FUNCTION set_iva_and_costo_unitario()
RETURNS TRIGGER AS $$
BEGIN
    -- Calcular IVA (13%)
    NEW.iva = NEW.compra_unitaria * 0.13;
    -- Calcular Costo Unitario (sin IVA)
    NEW.costo_unitario = NEW.compra_unitaria - NEW.iva;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;
');

DB::unprepared('
CREATE TRIGGER trigger_set_iva_and_costo_unitario
BEFORE INSERT ON inputs
FOR EACH ROW
EXECUTE FUNCTION set_iva_and_costo_unitario();
');

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
DECLARE
    remaining_quantity INTEGER := NEW.cantidad;
    current_entry RECORD;
BEGIN
    FOR current_entry IN
        SELECT id, cantidad_restante
        FROM inputs
        WHERE product_id = NEW.product_id
        ORDER BY fecha_entrada
    LOOP
        IF remaining_quantity <= 0 THEN
            EXIT;
        END IF;

        IF current_entry.cantidad_restante >= remaining_quantity THEN
            UPDATE inputs
            SET cantidad_restante = cantidad_restante - remaining_quantity
            WHERE id = current_entry.id;
            remaining_quantity := 0;
        ELSE
            remaining_quantity := remaining_quantity - current_entry.cantidad_restante;
            UPDATE inputs
            SET cantidad_restante = 0
            WHERE id = current_entry.id;
        END IF;
    END LOOP;

    UPDATE products
    SET stock_actual = stock_actual - NEW.cantidad
    WHERE id = NEW.product_id;

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
        DB::unprepared('DROP TRIGGER IF EXISTS trigger_set_cantidad_restante ON inputs');
        DB::unprepared('DROP FUNCTION IF EXISTS set_cantidad_restante');

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

        DB::unprepared('DROP TRIGGER IF EXISTS trigger_set_iva_and_costo_unitario ON inputs;');
        DB::unprepared('DROP FUNCTION IF EXISTS trigger_set_iva_and_costo_unitario();');
       
    }
};

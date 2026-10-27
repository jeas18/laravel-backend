<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Sample catalog: [name, description, price, base currency, tax_cost, manufacturing_cost].
     */
    private const PRODUCTS = [
        ['Laptop Pro 14', 'Portátil de 14 pulgadas, 16 GB de RAM y 512 GB SSD', 1200.00, 'USD', 228.00, 650.00],
        ['Monitor 27" 4K', 'Monitor IPS de 27 pulgadas con resolución 4K', 380.00, 'USD', 72.20, 190.00],
        ['Teclado mecánico', 'Teclado mecánico retroiluminado con switches rojos', 95.00, 'USD', 18.05, 40.00],
        ['Ratón inalámbrico', 'Ratón ergonómico inalámbrico con batería recargable', 45.00, 'EUR', 9.45, 15.00],
        ['Auriculares ANC', 'Auriculares con cancelación activa de ruido', 220.00, 'EUR', 46.20, 95.00],
        ['Silla ergonómica', 'Silla de oficina con soporte lumbar ajustable', 310.00, 'EUR', 65.10, 140.00],
        ['Webcam Full HD', 'Cámara web 1080p con micrófono integrado', 1250.00, 'MXN', 200.00, 520.00],
        ['Disco SSD 1 TB', 'Unidad de estado sólido NVMe de 1 TB', 1890.00, 'MXN', 302.40, 900.00],
        ['Hub USB-C', 'Adaptador USB-C 7 en 1 con HDMI y lector de tarjetas', 185000.00, 'COP', 35150.00, 70000.00],
        ['Lámpara LED de escritorio', 'Lámpara LED regulable con carga inalámbrica', 42.00, 'GBP', 8.40, 16.00],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = Currency::pluck('id', 'name');

        foreach (self::PRODUCTS as [$name, $description, $price, $currency, $taxCost, $manufacturingCost]) {
            Product::updateOrCreate(
                ['name' => $name],
                [
                    'description' => $description,
                    'price' => $price,
                    'currency_id' => $currencies[$currency],
                    'tax_cost' => $taxCost,
                    'manufacturing_cost' => $manufacturingCost,
                ],
            );
        }
    }
}

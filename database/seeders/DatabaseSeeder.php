<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductMedia;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $customer = Customer::updateOrCreate(
            ['id' => 233],
            [
                'full_name' => 'Vivianne Howell IV',
                'email' => 'vivianne.howell.233@example.com',
                'phone_country_code' => '+967',
                'phone' => '770351011',
                'password_hash' => 'Password123!',
                'category' => 'regular',
                'email_verified_at' => now(),
            ]
        );

        $address = Address::updateOrCreate(
            [
                'customer_id' => $customer->id,
                'recipient_name' => 'Vivianne Howell IV',
                'phone' => '770351011',
            ],
            [
                'country' => 'Yemen',
                'city' => 'Yvettemouth',
                'district' => 'Kendra Hills',
                'street' => '7055 Hegmann Circle Suite 494',
                'postal_code' => '13578',
                'is_default' => true,
            ]
        );

        $readyMadeOrder = Order::updateOrCreate(
            ['order_number' => 'WJ-233-1001'],
            [
                'customer_id' => $customer->id,
                'address_id' => $address->id,
                'shipping_recipient_name' => 'Vivianne Howell IV',
                'shipping_phone' => '770351011',
                'shipping_address_full' => '7055 Hegmann Circle Suite 494, Kendra Hills, Yvettemouth, Yemen',
                'shipping_city' => 'Yvettemouth',
                'shipping_country' => 'Yemen',
                'order_type' => 'ready_made',
                'status' => 'received',
                'expected_delivery_date' => now()->addDays(5)->toDateString(),
                'subtotal' => 320.00,
                'shipping_fee' => 25.00,
                'total_amount' => 345.00,
            ]
        );

        $customOrder = Order::updateOrCreate(
            ['order_number' => 'WJ-233-1002'],
            [
                'customer_id' => $customer->id,
                'address_id' => $address->id,
                'shipping_recipient_name' => 'Vivianne Howell IV',
                'shipping_phone' => '770351011',
                'shipping_address_full' => '7055 Hegmann Circle Suite 494, Kendra Hills, Yvettemouth, Yemen',
                'shipping_city' => 'Yvettemouth',
                'shipping_country' => 'Yemen',
                'order_type' => 'custom',
                'status' => 'in_production',
                'expected_delivery_date' => now()->addDays(12)->toDateString(),
                'subtotal' => 520.00,
                'shipping_fee' => 30.00,
                'total_amount' => 550.00,
            ]
        );

        $tableProduct = Product::updateOrCreate(
            ['sku' => 'WJ-TABLE-001'],
            [
                'name' => 'Handmade Wooden Table',
                'slug' => 'handmade-wooden-table',
                'description' => 'A handmade wooden table for the living room.',
                'price' => 120.00,
                'stock_quantity' => 20,
                'reserved_quantity' => 0,
                'is_customizable' => false,
                'is_handmade' => true,
                'status' => 'active',
            ]
        );

        $lampProduct = Product::updateOrCreate(
            ['sku' => 'WJ-LAMP-001'],
            [
                'name' => 'Decorative Table Lamp',
                'slug' => 'decorative-table-lamp',
                'description' => 'A decorative handmade table lamp.',
                'price' => 80.00,
                'stock_quantity' => 30,
                'reserved_quantity' => 0,
                'is_customizable' => false,
                'is_handmade' => true,
                'status' => 'active',
            ]
        );

        $customProduct = Product::updateOrCreate(
            ['sku' => 'WJ-CUSTOM-001'],
            [
                'name' => 'Custom Handmade Cabinet',
                'slug' => 'custom-handmade-cabinet',
                'description' => 'A custom handmade cabinet made to order.',
                'price' => 520.00,
                'stock_quantity' => 10,
                'reserved_quantity' => 0,
                'is_customizable' => true,
                'is_handmade' => true,
                'status' => 'active',
            ]
        );

        ProductMedia::updateOrCreate(
            [
                'product_id' => $tableProduct->id,
                'url' => 'https://images.unsplash.com/photo-1604578762246-41134e37f9cc?auto=format&fit=crop&w=640&q=85',
            ],
            [
                'media_type' => 'image',
                'sort_order' => 0,
                'is_primary' => true,
            ]
        );

        ProductMedia::updateOrCreate(
            [
                'product_id' => $lampProduct->id,
                'url' => 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?auto=format&fit=crop&w=640&q=85',
            ],
            [
                'media_type' => 'image',
                'sort_order' => 0,
                'is_primary' => true,
            ]
        );

        ProductMedia::updateOrCreate(
            [
                'product_id' => $customProduct->id,
                'url' => 'https://images.unsplash.com/photo-1558997519-83ea9252edf8?auto=format&fit=crop&w=640&q=85',
            ],
            [
                'media_type' => 'image',
                'sort_order' => 0,
                'is_primary' => true,
            ]
        );

        OrderItem::updateOrCreate(
            [
                'order_id' => $readyMadeOrder->id,
                'product_id' => $tableProduct->id,
            ],
            [
                'quantity' => 2,
                'unit_price' => 120.00,
                'is_customized' => false,
                'customization_note' => null,
            ]
        );

        OrderItem::updateOrCreate(
            [
                'order_id' => $readyMadeOrder->id,
                'product_id' => $lampProduct->id,
            ],
            [
                'quantity' => 1,
                'unit_price' => 80.00,
                'is_customized' => false,
                'customization_note' => null,
            ]
        );

        OrderItem::updateOrCreate(
            [
                'order_id' => $customOrder->id,
                'product_id' => $customProduct->id,
            ],
            [
                'quantity' => 1,
                'unit_price' => 520.00,
                'is_customized' => true,
                'customization_note' => 'Custom dimensions and walnut finish.',
            ]
        );

        $this->command->info('Customer, Address, sample Orders and Order Items seeded successfully for customer ID: 233');
    }
}
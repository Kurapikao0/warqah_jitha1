<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CustomDesignRequest;
use App\Models\CustomerNotification;
use App\Models\Address;
use App\Models\OrderProductionStage;

class SpecificCustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customerId = 233;

        // جلب أول مرحلة إنتاج متوفرة لتفادي خطأ الـ Unique Constraint
        $stageId = OrderProductionStage::first()?->id;

        // 1. العناوين (Addresses)
        Address::factory(2)->create([
            'customer_id' => $customerId,
        ]);

        // 2. السلة وعناصرها (Cart & CartItem)
        $cart = Cart::firstOrCreate(['customer_id' => $customerId]);
        CartItem::factory(3)->create([
            'cart_id' => $cart->id,
        ]);

        // 3. الطلبات وعناصرها (Order & OrderItem)
        Order::factory(3)->create([
            'customer_id' => $customerId,
            'current_production_stage_id' => $stageId,
        ])->each(function ($order) {
            OrderItem::factory(2)->create([
                'order_id' => $order->id,
            ]);
        });

        // 4. طلبات التخصيص (CustomDesignRequest)
        CustomDesignRequest::factory(2)->create([
            'customer_id' => $customerId,
        ]);

        // 5. إشعارات العميل (CustomerNotification)
        CustomerNotification::factory(4)->create([
            'customer_id' => $customerId,
        ]);
    }
}
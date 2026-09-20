namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Address;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. إنشاء العميل رقم 233 أو استرجاعه إذا كان موجوداً
        $customer = Customer::firstOrCreate(
            ['id' => 233],
            [
                'name' => 'Vivianne Howell IV',
                // إذا كان جدول العملاء يتطلب حقول إجبارية أخرى (مثل email أو password)، أضفها هنا
            ]
        );

        // 2. إدخال العنوان وربطه بالعميل مباشرة
        Address::create([
            'customer_id' => $customer->id,
            'recipient_name' => 'Vivianne Howell IV',
            'phone' => '770351011',
            'country' => 'Yemen',
            'city' => 'Yvettemouth',
            'district' => 'Kendra Hills',
            'street' => '7055 Hegmann Circle Suite 494',
            'postal_code' => '13578',
            'is_default' => 1,
        ]);

        $this->command->info('Customer and Address seeded successfully for customer ID: 233');
    }
}
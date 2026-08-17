<?php

namespace Tests\Feature\User;

use App\Enums\CustomerNotificationType;
use App\Models\Customer;
use App\Models\CustomerNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class CustomerNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        Gate::before(fn () => true);

        $this->customer = Customer::factory()->create();

        $token = $this->customer->createToken('customer-token')->plainTextToken;
        $this->withHeader('Authorization', "Bearer {$token}");
    }

    public function test_customer_can_list_their_notifications(): void
    {
        CustomerNotification::factory()->count(3)->create([
            'customer_id' => $this->customer->id,
        ]);

        $response = $this->getJson('/api/customer/notifications');

        $response->assertStatus(200);
    }

    public function test_customer_can_view_single_notification(): void
    {
        $notification = CustomerNotification::factory()->create([
            'customer_id' => $this->customer->id,
        ]);

        $response = $this->getJson("/api/customer/notifications/{$notification->id}");

        $response->assertStatus(200);
    }

    public function test_customer_can_mark_notification_as_read(): void
    {
        $notification = CustomerNotification::factory()->create([
            'customer_id' => $this->customer->id,
            'is_read' => false,
        ]);

        // استخدام PATCH مطابقاً للـ Route List الخاصة بالعميل
        $response = $this->patchJson("/api/customer/notifications/{$notification->id}/read");

        $response->assertStatus(200);

        $this->assertDatabaseHas('customer_notifications', [
            'id' => $notification->id,
            'is_read' => true,
        ]);
    }

    public function test_customer_can_delete_notification(): void
    {
        $notification = CustomerNotification::factory()->create([
            'customer_id' => $this->customer->id,
        ]);

        $response = $this->deleteJson("/api/customer/notifications/{$notification->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('customer_notifications', [
            'id' => $notification->id,
        ]);
    }

    public function test_notification_belongs_to_customer_and_casts_correctly(): void
    {
        $notification = CustomerNotification::factory()->create([
            'customer_id' => $this->customer->id,
            'is_read' => 1,
        ]);

        $this->assertInstanceOf(Customer::class, $notification->customer);
        $this->assertEquals($this->customer->id, $notification->customer->id);
        $this->assertIsBool($notification->is_read);
        $this->assertInstanceOf(CustomerNotificationType::class, $notification->type);
    }

    public function test_unauthenticated_user_cannot_access_customer_notifications(): void
    {
        $response = $this->flushHeaders()->getJson('/api/customer/notifications');

        $response->assertStatus(401);
    }
}

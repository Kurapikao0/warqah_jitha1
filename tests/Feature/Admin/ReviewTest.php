<?php

namespace Tests\Feature\Admin;

use App\Models\AdminUser;
use App\Models\Customer;
use App\Models\Review;
use App\Enums\ReviewStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    protected AdminUser $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // إنشاء أدمن وتوثيقه عبر guard الخاص بالأدمن
        $this->admin = AdminUser::factory()->create();
    }

    #[Test]
    public function admin_can_list_all_reviews(): void
    {
        Sanctum::actingAs($this->admin, ['*'], 'sanctum');

        Review::factory()->count(3)->create();

        $response = $this->getJson('/api/admin/reviews');

        $response->assertStatus(200);
    }

    #[Test]
    public function admin_review_response_includes_customer_and_product_names(): void
    {
        Sanctum::actingAs($this->admin, ['*'], 'sanctum');

        $customer = Customer::factory()->create(['full_name' => 'عميل تجريبي']);
        $product = \App\Models\Product::factory()->create(['name' => 'منتج تجريبي']);

        $review = Review::factory()->create([
            'customer_id' => $customer->id,
            'product_id' => $product->id,
            'comment' => 'تقييم تجريبي',
        ]);

        $response = $this->getJson('/api/admin/reviews');

        $response->assertOk()
            ->assertJsonPath('data.0.customer.full_name', 'عميل تجريبي')
            ->assertJsonPath('data.0.product.name', 'منتج تجريبي')
            ->assertJsonPath('data.0.customer_name', 'عميل تجريبي')
            ->assertJsonPath('data.0.product_name', 'منتج تجريبي');

        $this->assertDatabaseHas('reviews', ['id' => $review->id]);
    }

    #[Test]
    public function admin_can_filter_reviews_by_search_status_rating_and_date_range(): void
    {
        Sanctum::actingAs($this->admin, ['*'], 'sanctum');

        $matchedCustomer = Customer::factory()->create(['full_name' => 'أحمد المميز']);
        $otherCustomer = Customer::factory()->create(['full_name' => 'مستخدم آخر']);

        $matchedProduct = \App\Models\Product::factory()->create(['name' => 'منتج ممتاز']);
        $otherProduct = \App\Models\Product::factory()->create(['name' => 'منتج آخر']);

        Review::factory()->create([
            'customer_id' => $matchedCustomer->id,
            'product_id' => $matchedProduct->id,
            'comment' => 'تقييم ممتاز',
            'status' => ReviewStatus::Published->value,
            'rating' => 5,
            'created_at' => '2026-02-10 12:00:00',
        ]);

        Review::factory()->create([
            'customer_id' => $otherCustomer->id,
            'product_id' => $otherProduct->id,
            'comment' => 'تقييم آخر',
            'status' => ReviewStatus::Rejected->value,
            'rating' => 2,
            'created_at' => '2026-03-10 12:00:00',
        ]);

        $response = $this->getJson('/api/admin/reviews?search=أحمد&status=published&rating=5&date_from=2026-02-01&date_to=2026-02-28');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['comment' => 'تقييم ممتاز']);
        $response->assertJsonMissing(['comment' => 'تقييم آخر']);
    }

    #[Test]
    public function admin_can_approve_a_customer_review(): void
    {
        // 1. تسجيل الدخول بـ AdminUser المُنشأ في setUp
        Sanctum::actingAs($this->admin, ['*'], 'sanctum');

        // 2. إنشاء التقييم (يمكنك ربطه بـ Customer إذا كان الـ Factory يتطلب ذلك)
        $review = Review::factory()->create([
            'status' => ReviewStatus::Pending->value,
        ]);

        // 3. تجهيز الـ Payload
        $payload = [
            'status' => ReviewStatus::Published->value,
        ];

        // 4. تنفيذ الطلب
        $response = $this->putJson("/api/admin/reviews/{$review->id}/status", $payload);

        // 5. التأكد من النتيجة
        $response->assertStatus(200);

        $this->assertDatabaseHas('reviews', [
            'id'     => $review->id,
            'status' => ReviewStatus::Published->value,
        ]);
    }

    #[Test]
    public function admin_can_reply_to_a_review(): void
    {
        Sanctum::actingAs($this->admin, ['*'], 'sanctum');

        $review = Review::factory()->create();

        $payload = [
            'admin_reply' => 'شكراً لمشاركتك وتقييمك الرائع!',
        ];

        $response = $this->postJson("/api/admin/reviews/{$review->id}/reply", $payload);

        $response->assertStatus(200);

        $this->assertDatabaseHas('reviews', [
            'id'          => $review->id,
            'admin_reply' => 'شكراً لمشاركتك وتقييمك الرائع!',
        ]);
    }
}

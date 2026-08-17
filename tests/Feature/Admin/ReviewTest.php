<?php

namespace Tests\Feature\Admin;

use App\Enums\ReviewStatus;
use App\Models\AdminUser;
use App\Models\Customer;
use App\Models\Review;
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
        Sanctum::actingAs($this->admin, ['*'], 'admin');

        Review::factory()->count(3)->create();

        $response = $this->getJson('/api/admin/reviews');

        $response->assertStatus(200);
    }

    #[Test]
    public function admin_can_approve_a_customer_review(): void
    {
        // 1. تسجيل الدخول بـ AdminUser المُنشأ في setUp
        Sanctum::actingAs($this->admin, ['*'], 'admin');

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
            'id' => $review->id,
            'status' => ReviewStatus::Published->value,
        ]);
    }

    #[Test]
    public function admin_can_reply_to_a_review(): void
    {
        Sanctum::actingAs($this->admin, ['*'], 'admin');

        $review = Review::factory()->create();

        $payload = [
            'admin_reply' => 'شكراً لمشاركتك وتقييمك الرائع!',
        ];

        $response = $this->postJson("/api/admin/reviews/{$review->id}/reply", $payload);

        $response->assertStatus(200);

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'admin_reply' => 'شكراً لمشاركتك وتقييمك الرائع!',
        ]);
    }
}

<?php

namespace Tests\Feature\User;

use App\Enums\ReviewStatus;
use App\Models\Customer;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\ReviewImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    protected Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        // إنشاء عميل وتوثيقه
        $this->customer = Customer::factory()->create();
    }

    #[Test]
    public function unauthenticated_customer_cannot_access_reviews(): void
    {
        $response = $this->getJson('/api/customer/reviews');

        $response->assertStatus(401);
    }

    #[Test]
    public function authenticated_customer_can_list_their_reviews(): void
    {
        Sanctum::actingAs($this->customer, ['*'], 'sanctum');

        Review::factory()->count(3)->create([
            'customer_id' => $this->customer->id,
        ]);

        $response = $this->getJson('/api/customer/reviews');

        $response->assertStatus(200);
    }

    #[Test]
public function authenticated_customer_can_create_a_review(): void
        {
            Sanctum::actingAs($this->customer, ['*'], 'sanctum');

            $product = Product::factory()->create();

            // إنشاء الطلب وربطه بالعميل المسجل
            $order = \App\Models\Order::factory()->create([
                'customer_id' => $this->customer->id,
            ]);

            // ربط عنصر الطلب بالطلب الخاص بالعميل
            $orderItem = OrderItem::factory()->create([
                'order_id'   => $order->id,
                'product_id' => $product->id,
            ]);

            $payload = [
                'product_id'    => $product->id,
                'order_item_id' => $orderItem->id,
                'rating'        => 5,
                'comment'       => 'منتج ممتاز وجودة عالية جداً.',
            ];

            $response = $this->postJson('/api/customer/reviews', $payload);

            $response->assertStatus(201);

            $this->assertDatabaseHas('reviews', [
                'customer_id' => $this->customer->id,
                'product_id'  => $product->id,
                'rating'      => 5,
                'comment'     => 'منتج ممتاز وجودة عالية جداً.',
            ]);
        }

    #[Test]
    public function authenticated_customer_can_view_single_review(): void
    {
        Sanctum::actingAs($this->customer, ['*'], 'sanctum');

        $review = Review::factory()->create([
            'customer_id' => $this->customer->id,
        ]);

        $response = $this->getJson("/api/customer/reviews/{$review->id}");

        $response->assertStatus(200);
    }

    #[Test]
    public function authenticated_customer_can_update_their_review(): void
    {
        Sanctum::actingAs($this->customer, ['*'], 'sanctum');

        $review = Review::factory()->create([
            'customer_id' => $this->customer->id,
            'rating'      => 3,
            'comment'     => 'مقبول',
        ]);

        $payload = [
            'rating'  => 4,
            'comment' => 'تم تعديل الرأي: المنتج جيد بعد التجربة.',
        ];

        $response = $this->putJson("/api/customer/reviews/{$review->id}", $payload);

        $response->assertStatus(200);

        $this->assertDatabaseHas('reviews', [
            'id'      => $review->id,
            'rating'  => 4,
            'comment' => 'تم تعديل الرأي: المنتج جيد بعد التجربة.',
        ]);
    }

    #[Test]
    public function authenticated_customer_can_delete_their_review(): void
    {
        Sanctum::actingAs($this->customer, ['*'], 'sanctum');

        $review = Review::factory()->create([
            'customer_id' => $this->customer->id,
        ]);

        $response = $this->deleteJson("/api/customer/reviews/{$review->id}");

        $response->assertStatus(200);

        // التثبت من الحذف الناعم Soft Delete
        $this->assertSoftDeleted('reviews', [
            'id' => $review->id,
        ]);
    }

    #[Test]
    public function authenticated_customer_can_upload_image_to_review(): void
        {
            Storage::fake('public');
            Sanctum::actingAs($this->customer, ['*'], 'sanctum');

            $review = Review::factory()->create([
                'customer_id' => $this->customer->id,
            ]);

            // استخدام create بدلاً من image لتفادي الحاجة لمكتبة GD
            $payload = [
                'image' => UploadedFile::fake()->create('review_photo.jpg', 100, 'image/jpeg'),
            ];

            $response = $this->postJson("/api/customer/reviews/{$review->id}/images", $payload);

            $response->assertStatus(201);

            $this->assertDatabaseHas('review_images', [
                'review_id' => $review->id,
            ]);
        }

    #[Test]
    public function authenticated_customer_can_delete_review_image(): void
    {
        Sanctum::actingAs($this->customer, ['*'], 'sanctum');

        $review = Review::factory()->create([
            'customer_id' => $this->customer->id,
        ]);

        $reviewImage = ReviewImage::factory()->create([
            'review_id' => $review->id,
            'image_url' => 'reviews/sample.jpg',
        ]);

        $response = $this->deleteJson("/api/customer/review-images/{$reviewImage->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('review_images', [
            'id' => $reviewImage->id,
        ]);
    }
}
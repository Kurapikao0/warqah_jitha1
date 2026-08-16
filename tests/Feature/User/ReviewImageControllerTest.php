<?php

namespace Tests\Feature\User;

use App\Models\Customer;
use App\Models\Review;
use App\Models\ReviewImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReviewImageControllerTest extends TestCase
{
    use RefreshDatabase;

    protected Customer $customer;
    protected Review $review;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->customer = Customer::factory()->create();

        $this->review = Review::factory()->create([
            'customer_id' => $this->customer->id,
        ]);
    }

    /**
     * اختبار رفع الصورة بنجاح والتأكد من التخزين
     */
    public function test_customer_can_upload_review_image()
    {
        $this->actingAs($this->customer, 'customer');

        // استخدام create بدلاً من image لتفادي الحاجة لمكتبة GD
        $file = UploadedFile::fake()->create('review.jpg', 100, 'image/jpeg');

        $response = $this->postJson(
            "/api/customer/reviews/{$this->review->id}/images",
            [
                'image' => $file
            ]
        );

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'review_id',
                    'image_url'
                ]
            ]);

        $this->assertDatabaseHas('review_images', [
            'review_id' => $this->review->id
        ]);

        Storage::disk('public')->assertExists('reviews/' . $file->hashName());
    }

    /**
     * اختبار إلزامية رفع صورة
     */
    public function test_upload_review_image_requires_image()
    {
        $this->actingAs($this->customer, 'customer');

        $response = $this->postJson(
            "/api/customer/reviews/{$this->review->id}/images",
            []
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['image']);
    }

    /**
     * اختبار فشل رفع ملف بحجم أكبر من 2MB (2048KB)
     */
    public function test_upload_review_image_fails_if_size_exceeds_max_limit()
    {
        $this->actingAs($this->customer, 'customer');

        $file = UploadedFile::fake()->create('large_image.jpg', 3072, 'image/jpeg');

        $response = $this->postJson(
            "/api/customer/reviews/{$this->review->id}/images",
            [
                'image' => $file
            ]
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['image']);
    }

    /**
     * اختبار فشل رفع ملف بصيغة غير مسموحة
     */
    public function test_upload_review_image_fails_for_invalid_mime_type()
    {
        $this->actingAs($this->customer, 'customer');

        $file = UploadedFile::fake()->create('document.pdf', 500, 'application/pdf');

        $response = $this->postJson(
            "/api/customer/reviews/{$this->review->id}/images",
            [
                'image' => $file
            ]
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['image']);
    }

    /**
     * اختبار حذف الصورة
     */
    public function test_customer_can_delete_review_image()
    {
        $this->actingAs($this->customer, 'customer');

        $filePath = 'reviews/test.jpg';
        Storage::disk('public')->put($filePath, 'contents');

        $reviewImage = ReviewImage::factory()->create([
            'review_id' => $this->review->id,
            'image_url' => $filePath
        ]);

        $response = $this->deleteJson(
            "/api/customer/review-images/{$reviewImage->id}"
        );

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Review image deleted successfully'
            ]);

        $this->assertDatabaseMissing('review_images', [
            'id' => $reviewImage->id
        ]);

        Storage::disk('public')->assertMissing($filePath);
    }

    /**
     * اختبار منع الزائر من الرفع
     */
    public function test_guest_cannot_upload_review_image()
    {
        // استخدام create بدلاً من image
        $file = UploadedFile::fake()->create('review.jpg', 100, 'image/jpeg');

        $response = $this->postJson(
            "/api/customer/reviews/{$this->review->id}/images",
            [
                'image' => $file
            ]
        );

        $response->assertStatus(401);
    }

    /**
     * اختبار منع الزائر من الحذف
     */
    public function test_guest_cannot_delete_review_image()
    {
        $reviewImage = ReviewImage::factory()->create([
            'review_id' => $this->review->id
        ]);

        $response = $this->deleteJson(
            "/api/customer/review-images/{$reviewImage->id}"
        );

        $response->assertStatus(401);
    }
}

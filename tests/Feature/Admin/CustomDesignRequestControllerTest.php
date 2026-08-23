<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\AdminUser;
use App\Models\CustomDesignRequest;
use App\Models\CustomDesignRequestImage;
use App\Models\Customer;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CustomDesignRequestControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function actingAsAdmin(): AdminUser
    {
        $role = Role::factory()->create();

        $admin = AdminUser::factory()->create([
            'role_id' => $role->id,
        ]);

        Sanctum::actingAs($admin, ['*'], 'admin');

        return $admin;
    }

    protected function validPayload(
        array $overrides = []
    ): array {
        $customer = Customer::factory()->create();

        return array_merge([
            'customer_id' => $customer->id,
            'description' => 'طلب تصميم حر تجريبي.',
            'status' => 'new',
        ], $overrides);
    }

    public function test_guest_cannot_access_custom_design_request_endpoints(): void
    {
        $request = CustomDesignRequest::factory()->create();

        $this->getJson('/api/admin/custom-design-requests')
            ->assertUnauthorized();

        $this->postJson('/api/admin/custom-design-requests', [])
            ->assertUnauthorized();

        $this->getJson(
            "/api/admin/custom-design-requests/{$request->id}"
        )->assertUnauthorized();

        $this->putJson(
            "/api/admin/custom-design-requests/{$request->id}",
            []
        )->assertUnauthorized();

        $this->deleteJson(
            "/api/admin/custom-design-requests/{$request->id}"
        )->assertUnauthorized();
    }

    public function test_admin_can_list_custom_design_requests_with_images(): void
    {
        $this->actingAsAdmin();

        $request = CustomDesignRequest::factory()->create();

        $request->images()->create([
            'url' => 'https://example.com/design-1.jpg',
            'sort_order' => 1,
        ]);

        $response = $this->getJson(
            '/api/admin/custom-design-requests'
        );

        $response
            ->assertOk()
            ->assertJsonPath('data.0.id', $request->id)
            ->assertJsonPath(
                'data.0.images.0.url',
                'https://example.com/design-1.jpg'
            );
    }

    public function test_admin_can_create_custom_design_request_without_images(): void
    {
        $this->actingAsAdmin();

        $payload = $this->validPayload();

        $response = $this->postJson(
            '/api/admin/custom-design-requests',
            $payload
        );

        $response
            ->assertCreated()
            ->assertJsonPath(
                'data.customer.id',
                $payload['customer_id']
            )
            ->assertJsonPath(
                'data.description',
                $payload['description']
            );

        $this->assertDatabaseHas(
            'custom_design_requests',
            [
                'customer_id' => $payload['customer_id'],
                'description' => $payload['description'],
                'status' => $payload['status'],
            ]
        );
    }

    public function test_admin_can_create_custom_design_request_with_images(): void
    {
        Storage::fake('public');

        $this->actingAsAdmin();

        $payload = $this->validPayload();

        $imageOne = UploadedFile::fake()->create(
            'design-reference-one.jpg',
            100,
            'image/jpeg'
        );

        $imageTwo = UploadedFile::fake()->create(
            'design-reference-two.png',
            100,
            'image/png'
        );

        $response = $this->post(
            '/api/admin/custom-design-requests',
            [
                ...$payload,
                'images' => [
                    $imageOne,
                    $imageTwo,
                ],
            ]
        );

        $response
            ->assertCreated()
            ->assertJsonPath(
                'data.customer.id',
                $payload['customer_id']
            )
            ->assertJsonCount(2, 'data.images');

        $requestId = (int) $response->json('data.id');

        $this->assertDatabaseCount(
            'custom_design_request_images',
            2
        );

        $images = CustomDesignRequestImage::query()
            ->where('custom_design_request_id', $requestId)
            ->orderBy('sort_order')
            ->get();

        $this->assertCount(2, $images);
        $this->assertSame(1, $images[0]->sort_order);
        $this->assertSame(2, $images[1]->sort_order);

        foreach ($images as $image) {
            $path = parse_url(
                $image->url,
                PHP_URL_PATH
            );

            $storagePath = ltrim(
                str_replace('/storage/', '', (string) $path),
                '/'
            );

            $this->assertNotSame('', $storagePath);
            Storage::disk('public')->assertExists($storagePath);
        }
    }

    public function test_creating_custom_design_request_rejects_invalid_images(): void
    {
        $this->actingAsAdmin();

        $payload = $this->validPayload();

        $response = $this
            ->withHeader('Accept', 'application/json')
            ->post(
                '/api/admin/custom-design-requests',
                [
                    ...$payload,
                    'images' => [
                        UploadedFile::fake()->create(
                            'not-an-image.txt',
                            10,
                            'text/plain'
                        ),
                    ],
                ]
            );  

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'images.0',
            ]);
    }

    public function test_admin_can_view_custom_design_request_with_images(): void
    {
        $this->actingAsAdmin();

        $request = CustomDesignRequest::factory()->create();

        $request->images()->createMany([
            [
                'url' => 'https://example.com/design-one.jpg',
                'sort_order' => 1,
            ],
            [
                'url' => 'https://example.com/design-two.jpg',
                'sort_order' => 2,
            ],
        ]);

        $response = $this->getJson(
            "/api/admin/custom-design-requests/{$request->id}"
        );

        $response
            ->assertOk()
            ->assertJsonPath(
                'data.id',
                $request->id
            )
            ->assertJsonCount(
                2,
                'data.images'
            )
            ->assertJsonPath(
                'data.images.0.sort_order',
                1
            )
            ->assertJsonPath(
                'data.images.1.sort_order',
                2
            );
    }

    public function test_admin_can_update_custom_design_request_and_add_images(): void
    {
        Storage::fake('public');

        $this->actingAsAdmin();

        $request = CustomDesignRequest::factory()->create([
            'description' => 'الوصف القديم',
            'status' => 'new',
        ]);

        $file = UploadedFile::fake()->create(
            'new-reference.jpg',
            100,
            'image/jpeg'
        );

        $response = $this->put(
            "/api/admin/custom-design-requests/{$request->id}",
            [
                'description' => 'الوصف المحدث',
                'status' => 'in_review',
                'images' => [
                    $file,
                ],
            ]
        );

        $response
            ->assertOk()
            ->assertJsonPath(
                'data.description',
                'الوصف المحدث'
            )
            ->assertJsonPath(
                'data.status',
                'in_review'
            )
            ->assertJsonCount(
                1,
                'data.images'
            );

        $this->assertDatabaseHas(
            'custom_design_requests',
            [
                'id' => $request->id,
                'description' => 'الوصف المحدث',
                'status' => 'in_review',
            ]
        );

        $image = CustomDesignRequestImage::query()
            ->where('custom_design_request_id', $request->id)
            ->first();

        $this->assertNotNull($image);

        $path = parse_url(
            $image->url,
            PHP_URL_PATH
        );

        $storagePath = ltrim(
            str_replace('/storage/', '', (string) $path),
            '/'
        );

        Storage::disk('public')->assertExists($storagePath);
    }

    public function test_admin_can_delete_custom_design_request_and_its_images(): void
    {
        Storage::fake('public');

        $this->actingAsAdmin();

        $request = CustomDesignRequest::factory()->create();

        $path = "custom-design-requests/{$request->id}/existing.jpg";

        Storage::disk('public')->put(
            $path,
            'fake-image'
        );

        $request->images()->create([
            'url' => asset(
                Storage::disk('public')->url($path)
            ),
            'sort_order' => 1,
        ]);

        $response = $this->deleteJson(
            "/api/admin/custom-design-requests/{$request->id}"
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing(
            'custom_design_requests',
            [
                'id' => $request->id,
            ]
        );

        $this->assertDatabaseMissing(
            'custom_design_request_images',
            [
                'custom_design_request_id' => $request->id,
            ]
        );

        Storage::disk('public')->assertMissing($path);
    }

    public function test_creating_custom_design_request_requires_customer_and_description(): void
    {
        $this->actingAsAdmin();

        $response = $this->postJson(
            '/api/admin/custom-design-requests',
            []
        );

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'customer_id',
                'description',
            ]);
    }
    public function test_admin_can_delete_single_custom_design_image(): void
    {
        Storage::fake('public');

        $this->actingAsAdmin();

        $request = CustomDesignRequest::factory()->create();

        $path = "custom-design-requests/{$request->id}/single.jpg";

        Storage::disk('public')->put($path, 'fake-image');

        $image = $request->images()->create([
            'url' => asset(
                Storage::disk('public')->url($path)
            ),
            'sort_order' => 1,
        ]);

        $response = $this->deleteJson(
            "/api/admin/custom-design-requests/{$request->id}/images/{$image->id}"
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing(
            'custom_design_request_images',
            [
                'id' => $image->id,
            ]
        );

        Storage::disk('public')->assertMissing($path);
    }

    public function test_admin_cannot_delete_image_belonging_to_another_custom_design_request(): void
    {
        $this->actingAsAdmin();

        $request = CustomDesignRequest::factory()->create();
        $otherRequest = CustomDesignRequest::factory()->create();

        $image = $otherRequest->images()->create([
            'url' => 'https://example.com/other.jpg',
            'sort_order' => 1,
        ]);

        $response = $this->deleteJson(
            "/api/admin/custom-design-requests/{$request->id}/images/{$image->id}"
        );

        $response->assertNotFound();

        $this->assertDatabaseHas(
            'custom_design_request_images',
            [
                'id' => $image->id,
            ]
        );
    }
}

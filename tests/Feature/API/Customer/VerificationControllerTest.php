<?php

namespace Tests\Feature\API\Customer;

use App\Enums\VerificationPurpose;
use App\Models\Customer;
use App\Models\VerificationCode;
use App\Services\VerificationCodeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class VerificationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected Customer $customer;

    protected string $generateUrl = '/api/customer/verifications/generate';
    protected string $verifyUrl = '/api/customer/verifications/verify';

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = Customer::factory()->create();
        Sanctum::actingAs($this->customer, ['*'], 'sanctum');

        if (\Route::has('customer.verification.generate')) {
            $this->generateUrl = route('customer.verification.generate');
        } elseif (\Route::has('verification.generate')) {
            $this->generateUrl = route('verification.generate');
        }

        if (\Route::has('customer.verification.verify')) {
            $this->verifyUrl = route('customer.verification.verify');
        } elseif (\Route::has('verification.verify')) {
            $this->verifyUrl = route('verification.verify');
        }
    }

    protected function getValidPurpose(): string
    {
        if (enum_exists(VerificationPurpose::class)) {
            $cases = VerificationPurpose::cases();
            if (!empty($cases)) {
                return $cases[0]->value ?? $cases[0]->name;
            }
        }

        return 'phone_verification';
    }

    #[Test]
    public function يمكن_للعميل_توليد_رمز_تحقق_بنجاح(): void
    {
        $purpose = $this->getValidPurpose();
        $contactValue = '0501234567';

        // إنشاء نموذج VerificationCode حقيقي أو وهمي يتطابق مع الـ Return Type
        $verificationModel = class_exists(VerificationCode::class) && method_exists(VerificationCode::class, 'factory')
            ? VerificationCode::factory()->make()
            : new VerificationCode();

        $this->mock(VerificationCodeService::class, function (MockInterface $mock) use ($verificationModel) {
            $mock->shouldReceive('generate')
                ->once()
                ->andReturn($verificationModel);
        });

        $payload = [
            'purpose' => $purpose,
            'contact_value' => $contactValue,
        ];

        $response = $this->postJson($this->generateUrl, $payload);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Verification generated successfully.',
            ]);
    }

    #[Test]
    public function يرفض_توليد_رمز_التحقق_عند_نقص_البيانات(): void
    {
        $response = $this->postJson($this->generateUrl, []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['purpose', 'contact_value']);
    }

    #[Test]
    public function يمكن_للعميل_التحقق_من_الرمز_بنجاح(): void
    {
        $purpose = $this->getValidPurpose();
        $contactValue = '0501234567';
        $code = '123456';

        $this->mock(VerificationCodeService::class, function (MockInterface $mock) {
            $mock->shouldReceive('verify')
                ->once()
                ->andReturn(true);
        });

        $payload = [
            'purpose' => $purpose,
            'contact_value' => $contactValue,
            'code_or_token' => $code,
        ];

        $response = $this->postJson($this->verifyUrl, $payload);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Verification completed successfully.',
            ]);
    }

    #[Test]
    public function يرفض_التحقق_عندما_يكون_الرمز_غير_صحيح_أو_منتهي_الصلاحية(): void
    {
        $purpose = $this->getValidPurpose();

        $this->mock(VerificationCodeService::class, function (MockInterface $mock) {
            $mock->shouldReceive('verify')
                ->once()
                ->andReturn(false);
        });

        $payload = [
            'purpose' => $purpose,
            'contact_value' => '0501234567',
            'code_or_token' => '999999',
        ];

        $response = $this->postJson($this->verifyUrl, $payload);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Invalid or expired verification code.',
            ]);
    }

    #[Test]
    public function يرفض_التحقق_عند_عدم_تمرير_رمز_التحقق(): void
    {
        $payload = [
            'purpose' => $this->getValidPurpose(),
            'contact_value' => '0501234567',
        ];

        $response = $this->postJson($this->verifyUrl, $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['code_or_token']);
    }
}

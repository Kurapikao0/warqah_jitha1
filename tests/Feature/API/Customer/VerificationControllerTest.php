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
        Sanctum::actingAs($this->customer, ['*'], 'customer');

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

        $verificationModel = class_exists(VerificationCode::class) && method_exists(VerificationCode::class, 'factory')
            ? VerificationCode::factory()->make()
            : new VerificationCode();

        $this->mock(VerificationCodeService::class, function (MockInterface $mock) use ($verificationModel) {
            $mock->shouldReceive('generateCode')
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

        $this->mock(VerificationCodeService::class, function (MockInterface $mock) {
            $mock->shouldReceive('verify')
                ->once()
                ->andReturn(true);
        });

        $response = $this->postJson($this->verifyUrl, [
            'purpose' => $purpose,
            'contact_value' => '0501234567',
            'code_or_token' => '123456',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Verification completed successfully.',
            ]);
    }

    #[Test]
    public function يرفض_التحقق_عندما_يكون_الرمز_غير_صحيح_أو_منتهي_الصلاحية(): void
    {
        $this->mock(VerificationCodeService::class, function (MockInterface $mock) {
            $mock->shouldReceive('verify')
                ->once()
                ->andReturn(false);
        });

        $response = $this->postJson($this->verifyUrl, [
            'purpose' => $this->getValidPurpose(),
            'contact_value' => '0501234567',
            'code_or_token' => '999999',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Invalid or expired verification code.',
            ]);
    }

    #[Test]
    public function يرفض_التحقق_عند_عدم_تمرير_رمز_التحقق(): void
    {
        $response = $this->postJson($this->verifyUrl, [
            'purpose' => $this->getValidPurpose(),
            'contact_value' => '0501234567',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['code_or_token']);
    }
}

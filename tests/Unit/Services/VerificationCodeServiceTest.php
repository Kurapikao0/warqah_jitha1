<?php

namespace Tests\Unit\Services;

use App\Enums\VerificationPurpose;
use App\Models\Customer;
use App\Models\VerificationCode;
use App\Services\VerificationCodeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class VerificationCodeServiceTest extends TestCase
{
    use RefreshDatabase;

    protected VerificationCodeService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new VerificationCodeService();
    }

    #[Test]
    public function it_generates_verification_code_for_a_customer(): void
    {
        $customer = Customer::factory()->create();

        // الدالة الفعلية بالخدمة اسمها generate() وتحتاج purpose و contactValue
        $verificationCode = $this->service->generate(
            $customer,
            VerificationPurpose::SignupEmailVerification,
            $customer->email
        );

        $this->assertDatabaseHas('verification_codes', [
            'id'          => $verificationCode->id,
            'customer_id' => $customer->id,
        ]);
    }

    #[Test]
    public function it_can_create_verification_code_using_factory(): void
    {
        $code = VerificationCode::factory()->create();

        $this->assertNotNull($code->customer_id);
        $this->assertDatabaseHas('verification_codes', [
            'id' => $code->id,
        ]);
    }
}
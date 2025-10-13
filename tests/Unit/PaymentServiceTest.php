<?php

namespace Tests\Unit;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    private PaymentService $paymentService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentService = new PaymentService();
    }

    public function test_payment_processing_creates_payment_record(): void
    {
        $booking = Booking::factory()->create();

        $payment = $this->paymentService->process($booking);

        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertContains($payment->status, ['success', 'failed']);
        $this->assertEquals($booking->total_amount, $payment->amount);
    }

    public function test_refund_updates_payment_status(): void
    {
        $payment = Payment::factory()->create(['status' => 'success']);

        $result = $this->paymentService->refund($payment);

        $this->assertTrue($result);
        $this->assertEquals('refunded', $payment->fresh()->status);
    }

    public function test_refund_fails_for_non_successful_payment(): void
    {
        $payment = Payment::factory()->create(['status' => 'failed']);

        $result = $this->paymentService->refund($payment);

        $this->assertFalse($result);
        $this->assertEquals('failed', $payment->fresh()->status);
    }
}

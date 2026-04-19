<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\BookingSeat;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Test QR Check-in Logic
 * 
 * Verifies that:
 * - QR codes with 'cancelled' status cannot be used for check-in
 * - QR codes with 'checked' status cannot be reused
 * - Only 'active' QR codes can be checked in
 */
class BookingSeatQRTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that cancelled QR codes are rejected with specific message
     */
    public function test_cancelled_qr_code_cannot_check_in()
    {
        // Create a booking seat with cancelled QR
        $bookingSeat = BookingSeat::factory()->create([
            'qr_code' => 'test_qr_cancelled',
            'qr_status' => 'cancelled',
        ]);

        $result = BookingSeat::checkInWithQR('test_qr_cancelled');

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('cancelled', strtolower($result['message']));
    }

    /**
     * Test that already checked QR codes are rejected
     */
    public function test_checked_qr_code_cannot_be_reused()
    {
        $bookingSeat = BookingSeat::factory()->create([
            'qr_code' => 'test_qr_checked',
            'qr_status' => 'checked',
            'checked_at' => now(),
        ]);

        $result = BookingSeat::checkInWithQR('test_qr_checked');

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('already', strtolower($result['message']));
    }

    /**
     * Test that active QR codes can check in successfully
     */
    public function test_active_qr_code_checks_in_successfully()
    {
        $bookingSeat = BookingSeat::factory()->create([
            'qr_code' => 'test_qr_active',
            'qr_status' => 'active',
        ]);

        $result = BookingSeat::checkInWithQR('test_qr_active');

        $this->assertTrue($result['success']);
        
        // Verify status changed to 'checked'
        $bookingSeat->refresh();
        $this->assertEquals('checked', $bookingSeat->qr_status);
        $this->assertNotNull($bookingSeat->checked_at);
    }

    /**
     * Test that invalid QR codes are rejected
     */
    public function test_invalid_qr_code_is_rejected()
    {
        $result = BookingSeat::checkInWithQR('non_existent_qr');

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('invalid', strtolower($result['message']));
    }
}

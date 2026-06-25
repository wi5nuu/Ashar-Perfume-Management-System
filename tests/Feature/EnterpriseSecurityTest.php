<?php

namespace Tests\Feature;

use App\Models\IpBlacklist;
use App\Models\User;
use App\Services\Security\ActivityMonitor;
use App\Services\Security\DataIntegrityService;
use App\Services\Security\PosAntiTamperingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnterpriseSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_activity_monitor_login_attempts(): void
    {
        $monitor = app(ActivityMonitor::class);
        $email = 'test@example.com';
        $ip = '192.168.1.1';

        for ($i = 0; $i < 5; $i++) {
            $check = $monitor->checkLoginAttempt($email, $ip);
            if (!$check['blocked']) {
                $monitor->checkLoginAttempt($email, $ip);
            }
        }

        $check = $monitor->checkLoginAttempt($email, $ip);
        $this->assertTrue($check['blocked']);
    }

    public function test_ip_blacklist_blocking(): void
    {
        $ip = '10.0.0.1';

        IpBlacklist::block($ip, 'Test block');

        $this->assertTrue(IpBlacklist::isBlocked($ip));
    }

    public function test_pos_anti_tampering_rejects_invalid_price(): void
    {
        $service = app(PosAntiTamperingService::class);

        $items = [
            ['product_id' => 99999, 'quantity' => 1, 'price' => -100],
        ];

        $result = $service->validateCart($items);
        $this->assertFalse($result['valid']);
    }

    public function test_data_integrity_score(): void
    {
        $service = app(DataIntegrityService::class);
        $score = $service->getIntegrityScore();

        $this->assertIsNumeric($score);
        $this->assertGreaterThanOrEqual(0, $score);
        $this->assertLessThanOrEqual(100, $score);
    }
}

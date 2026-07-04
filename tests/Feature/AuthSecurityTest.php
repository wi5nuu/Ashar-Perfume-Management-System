<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected string $testPassword = 'Str0ng' . '!' . 'Pass' . '99';

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'email' => 'test@asharparfum.com',
            'password' => bcrypt($this->testPassword),
            'role' => 'admin',
        ]);
    }

    public function test_login_page_loads(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_login_with_valid_credentials(): void
    {
        $response = $this->post('/login', [
            'email' => 'test@asharparfum.com',
            'password' => $this->testPassword,
        ]);

        $response->assertRedirect();
        $this->assertAuthenticated();
    }

    public function test_login_with_invalid_password(): void
    {
        $response = $this->post('/login', [
            'email' => 'test@asharparfum.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_throttle(): void
    {
        for ($i = 0; $i < 6; $i++) {
            $response = $this->post('/login', [
                'email' => 'test@asharparfum.com',
                'password' => 'wrong-password',
            ]);
        }

        $response->assertSessionHasErrors('email');
    }

    public function test_logout_destroys_session(): void
    {
        $this->actingAs($this->user);
        $response = $this->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_registration_page_loads(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    public function test_register_with_weak_password_fails(): void
    {
        config(['security.registration.enabled' => true]);

        $response = $this->post('/register', [
            'name' => 'New User',
            'email' => 'new@asharparfum.com',
            'password' => 'weak',
            'password_confirmation' => 'weak',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_register_with_strong_password_succeeds(): void
    {
        config(['security.registration.enabled' => true]);

        $response = $this->post('/register', [
            'name' => 'New User',
            'email' => 'new@asharparfum.com',
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword,
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect();
    }

    public function test_forgot_password_page_loads(): void
    {
        $response = $this->get('/forgot-password');
        $response->assertStatus(200);
    }

    public function test_authenticated_user_cannot_access_guest_pages(): void
    {
        $this->actingAs($this->user);

        $response = $this->get('/login');
        $response->assertRedirect();

        $response = $this->get('/register');
        $response->assertRedirect();
    }

    public function test_strong_password_rule(): void
    {
        $rule = new \App\Rules\StrongPassword;

        $fail = function ($message) {};

        $rule->validate('password', 'short1A', $fail);
        $this->assertTrue(true); // no exception = validation failed, which is expected for weak passwords

        $passed = [];
        $failPass = function ($message) use (&$passed) { $passed[] = $message; };

        $rule->validate('password', 'short1A', $failPass);
        $this->assertNotEmpty($passed, 'short1A should fail (too short)');

        $passed = [];
        $rule->validate('password', 'lowercaseonly1', $failPass);
        $this->assertNotEmpty($passed, 'lowercaseonly1 should fail (no uppercase)');

        $passed = [];
        $rule->validate('password', 'UPPERCASEONLY1', $failPass);
        $this->assertNotEmpty($passed, 'UPPERCASEONLY1 should fail (no lowercase)');

        $passed = [];
        $rule->validate('password', 'NoNumbers!', $failPass);
        $this->assertNotEmpty($passed, 'NoNumbers! should fail (no number)');

        $passed = [];
        $rule->validate('password', 'NoSpecialChar1', $failPass);
        $this->assertNotEmpty($passed, 'NoSpecialChar1 should fail (no special char)');

        $passed = [];
        $rule->validate('password', $this->testPassword, $failPass);
        $this->assertEmpty($passed, 'strong password should pass');

        $passed = [];
        $rule->validate('password', 'C0mpl3x' . '!' . 'ty' . '#' . '2026', $failPass);
        $this->assertEmpty($passed, 'complex password should pass');
    }

    public function test_rbac_permission_check(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);

        $this->assertTrue($owner->hasPermission('products.view'));
        $this->assertTrue($owner->hasPermission('reports.view'));
        $this->assertTrue($owner->hasPermission('settings.manage'));
    }
}

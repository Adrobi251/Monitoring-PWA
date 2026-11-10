<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login_from_protected_pages()
    {
        $urls = [
            route('dashboard'),
            route('profile.edit'),
            route('notifications.index'),
            route('mahasiswa.dashboard'),
            route('mahasiswa.jadwal'),
            route('mahasiswa.riwayat'),
            route('mahasiswa.pengajuan.create'),
            route('dosen.dashboard'),
            route('dosen.jadwal'),
            route('dosen.riwayat'),
            route('dosen.mahasiswa.index'),
        ];

        foreach ($urls as $url) {
            $this->get($url)->assertRedirect(route('login'));
        }
    }

    public function test_login_fails_with_wrong_password_and_keeps_user_guest()
    {
        User::factory()->create([
            'email' => 'target@test.com',
            'password' => 'password',
        ]);

        $this->post(route('login'), [
            'email' => 'target@test.com',
            'password' => 'salah-total',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_login_is_rate_limited_after_five_failures()
    {
        User::factory()->create([
            'email' => 'target@test.com',
            'password' => 'password',
        ]);

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->post(route('login'), [
                'email' => 'target@test.com',
                'password' => 'salah-'.$attempt,
            ])->assertSessionHasErrors('email');
        }

        // Password benar pun harus tetap ditolak selama lockout aktif.
        $this->post(route('login'), [
            'email' => 'target@test.com',
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_logout_ends_the_authenticated_session()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect('/');

        $this->assertGuest();
    }

    public function test_registration_always_creates_mahasiswa_even_if_role_is_posted()
    {
        $response = $this->post(route('register'), [
            'name' => 'Calon Mahasiswa',
            'email' => 'calon@test.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'nim' => 'NIM-BARU',
            'role' => 'dosen',
            'nidn' => 'NIDN-PALSU',
        ]);

        $response->assertRedirect(route('dashboard'));

        $user = User::where('email', 'calon@test.com')->firstOrFail();

        $this->assertSame('mahasiswa', $user->role);
        $this->assertNull($user->nidn);
        $this->assertNull($user->dosen_wali_id);
    }

    public function test_registration_rejects_duplicate_nim_with_validation_error()
    {
        User::factory()->create([
            'role' => 'mahasiswa',
            'nim' => 'NIM001',
        ]);

        $response = $this->post(route('register'), [
            'name' => 'Mahasiswa Kedua',
            'email' => 'kedua@test.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'nim' => 'NIM001',
        ]);

        $response->assertSessionHasErrors('nim');

        $this->assertSame(1, User::where('nim', 'NIM001')->count());
        $this->assertNull(User::where('email', 'kedua@test.com')->first());
    }

    public function test_profile_update_cannot_escalate_role_or_change_identity_fields()
    {
        $user = User::factory()->create([
            'role' => 'mahasiswa',
            'nim' => 'NIM001',
        ]);

        $this->actingAs($user)
            ->patch(route('profile.update'), [
                'name' => 'Nama Baru',
                'email' => 'baru@test.com',
                'role' => 'dosen',
                'nim' => 'NIM-HACKED',
                'nidn' => 'NIDN-HACKED',
                'dosen_wali_id' => 999,
            ])
            ->assertRedirect(route('profile.edit'));

        $user->refresh();

        $this->assertSame('Nama Baru', $user->name);
        $this->assertSame('baru@test.com', $user->email);
        $this->assertSame('mahasiswa', $user->role);
        $this->assertSame('NIM001', $user->nim);
        $this->assertNull($user->nidn);
        $this->assertNull($user->dosen_wali_id);
    }

    public function test_passwords_are_stored_hashed()
    {
        $user = User::factory()->create(['password' => 'rahasia-123']);

        $this->assertNotSame('rahasia-123', $user->password);
        $this->assertTrue(password_verify('rahasia-123', $user->password));
    }

    public function test_session_cookie_defaults_are_hardened()
    {
        $this->assertTrue(config('session.http_only'));
        $this->assertSame('lax', config('session.same_site'));
    }

    public function test_security_headers_are_set_and_php_version_hidden()
    {
        $response = $this->get(route('login'));

        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeaderMissing('X-Powered-By');
    }
}

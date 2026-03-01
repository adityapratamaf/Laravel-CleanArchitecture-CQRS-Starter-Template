<?php

namespace Tests\Feature\Web;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Infrastructure\Persistence\Eloquent\Models\UserModel;
use Illuminate\Support\Facades\Hash;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_web_login_success_redirects_to_users(): void
    {
        UserModel::create([
            'name' => 'Admin',
            'email' => 'admin@project.com',
            'password' => Hash::make('password123'),
        ]);

        $res = $this->post('/login', [
            'email' => 'admin@project.com',
            'password' => 'password123',
        ]);

        $res->assertRedirect('/users');
        $this->assertAuthenticated();
    }

    public function test_web_logout_logs_user_out(): void
    {
        $user = UserModel::create([
            'name' => 'Admin',
            'email' => 'admin@project.com',
            'password' => Hash::make('password123'),
        ]);

        $this->actingAs($user);

        $res = $this->post('/logout');

        $res->assertRedirect('/login');
        $this->assertGuest();
    }
}
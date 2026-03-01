<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Infrastructure\Persistence\Eloquent\Models\UserModel;
use Illuminate\Support\Facades\Hash;

class UserCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_login_returns_token(): void
    {
        $user = UserModel::create([
            'name' => 'Admin',
            'email' => 'admin@project.com',
            'password' => Hash::make('password123'),
        ]);

        $res = $this->postJson('/api/login', [
            'email' => 'admin@project.com',
            'password' => 'password123',
            'device_name' => 'test',
        ]);

        $res->assertStatus(200)
            ->assertJsonStructure(['token', 'token_type', 'user' => ['id','name','email']]);
    }

    public function test_api_users_crud_with_sanctum_token(): void
    {
        $admin = UserModel::create([
            'name' => 'Admin',
            'email' => 'admin@project.com',
            'password' => Hash::make('password123'),
        ]);

        $token = $admin->createToken('test')->plainTextToken;

        // CREATE
        $create = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/users', [
                'name' => 'U1',
                'email' => 'u1@example.com',
                'password' => 'password123',
            ]);

        $create->assertStatus(201)
            ->assertJsonStructure(['id','name','email']);

        $id = $create->json('id');

        // LIST
        $list = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/users');

        $list->assertStatus(200)
            ->assertJsonStructure(['data', 'meta']);

        // UPDATE
        $upd = $this->withHeader('Authorization', "Bearer {$token}")
            ->putJson("/api/users/{$id}", [
                'name' => 'U1 Updated',
                'email' => 'u1updated@example.com',
            ]);

        $upd->assertStatus(200)
            ->assertJsonPath('name', 'U1 Updated');

        // DELETE
        $del = $this->withHeader('Authorization', "Bearer {$token}")
            ->deleteJson("/api/users/{$id}");

        $del->assertStatus(200)
            ->assertJsonPath('message', 'User deleted');
    }

    public function test_logout_revokes_token(): void
    {
        $admin = UserModel::create([
            'name' => 'Admin',
            'email' => 'admin@project.com',
            'password' => Hash::make('password123'),
        ]);

        $token = $admin->createToken('test')->plainTextToken;
        $tokenId = (int) explode('|', $token, 2)[0];

        $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/logout')
            ->assertStatus(200);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $tokenId,
        ]);

        $this->app['auth']->forgetGuards();

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/me')
            ->assertStatus(401);
    }
}
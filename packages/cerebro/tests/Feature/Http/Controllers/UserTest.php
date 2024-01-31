<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    protected $userData;
    protected $admin;
    protected $user;
    protected $field;
    protected $method;
    protected $url;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:refresh');
        $this->artisan('db:seed --class=UsersAndNotesSeeder');
        $userData = User::factory()->make()->toArray();
        $userData['password'] = 'password';
        $userData['password_confirmation'] = 'password';
        $this->userData = $userData;
        $this->admin = User::find(1);
        $this->user = User::find(2);
    }

    protected function test_incorrect_data($userData)
    {
        $response = $this->actingAs($this->admin)->json($this->method, $this->url, $userData);
        $response->assertStatus(422);
        $response->assertJsonValidationErrorFor($this->field);
    }

    protected function test_required_field()
    {
        $userData = $this->userData;
        unset($userData[$this->field]);
        $this->test_incorrect_data($userData);
    }

    protected function test_numeric_field()
    {
        $userData = $this->userData;

        $userData[$this->field] = 'its not numeric';
        $this->test_incorrect_data($userData);

        $userData[$this->field] = '123its not numeric123';
        $this->test_incorrect_data($userData);

        $userData[$this->field] = '123#123';
        $this->test_incorrect_data($userData);

        $userData[$this->field] = '123a123';
        $this->test_incorrect_data($userData);

        $userData[$this->field] = '1+1';
        $this->test_incorrect_data($userData);

        $userData[$this->field] = 'true';
        $this->test_incorrect_data($userData);

        $userData[$this->field] = true;
        $this->test_incorrect_data($userData);

        $userData[$this->field] = '-1';
        $this->test_incorrect_data($userData);
    }

    protected function test_string_field()
    {
        $userData = $this->userData;

        $userData[$this->field] = 123;
        $this->test_incorrect_data($userData);

        $userData[$this->field] = true;
        $this->test_incorrect_data($userData);

        $userData[$this->field] = null;
        $this->test_incorrect_data($userData);
    }

    protected function test_min1_max256()
    {
        $userData = $this->userData;

        $userData[$this->field] = '';
        $this->test_incorrect_data($userData);

        $userData[$this->field] = 'qweqweqaqweqweqaqweqweqaqweqweqaqweqweqaqweqweqaqweqweqaqweqweqaqweqweqaqweqweqaqweqweqaqweqweqaqweqweqaqweqweqaqweqweqaqweqweqaqweqweqaqweqweqaqweqweqaqweqweqaqweqweqaqweqweqaqweqweqaqweqweqaqweqweqaqweqweqaqweqweqaqweqweqaqweqweqaqweqweqaqweqweqaqweqweqaq';//257
        $this->test_incorrect_data($userData);
    }

    protected function config_store(){
        $this->url = '/users';
        $this->method = 'POST';
    }

    protected function config_update(){
        $this->url = '/users/3';
        $this->method = 'PUT';
    }

    public function test_store(){
        $response = $this->json('post', '/users', $this->userData);
        $response->assertStatus(401);

        $response = $this->actingAs($this->user)->json('post', '/users', $this->userData);
        $response->assertStatus(401);

        $userCount = User::all()->count();

        $response = $this->actingAs($this->admin)->json('post', '/users', $this->userData);
        $response->assertStatus(200);
        $this->assertDatabaseCount('users', $userCount + 1);
    }

    public function test_validate_wrong_name_store(){
        $this->field = 'name';
        $this->config_store();

        $this->test_required_field();
        $this->test_min1_max256();
    }
}

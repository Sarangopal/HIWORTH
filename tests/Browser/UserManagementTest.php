<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class UserManagementTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_can_create_user(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/users')
                ->assertSee('Users Management')
                ->clickLink('Create User')
                ->assertPathIs('/users/create')
                ->assertSee('Create New User')
                ->type('name', 'John Doe')
                ->type('email', 'john@example.com')
                ->type('password', 'password123')
                ->press('Create User')
                ->assertPathIs('/users')
                ->assertSee('User created successfully')
                ->assertSee('John Doe')
                ->assertSee('john@example.com');
        });
    }

    public function test_validation_errors_display_when_creating_user(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/users/create')
                ->press('Create User')
                ->assertSee('The name field is required')
                ->assertSee('The email field is required')
                ->assertSee('The password field is required');
        });
    }

    public function test_email_validation_on_user_creation(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/users/create')
                ->type('name', 'John Doe')
                ->type('email', 'invalid-email')
                ->type('password', 'password123')
                ->press('Create User')
                ->assertSee('The email field must be a valid email address');
        });
    }

    public function test_can_list_users(): void
    {
        User::factory()->count(3)->create();

        $this->browse(function (Browser $browser) {
            $browser->visit('/users')
                ->assertSee('Users Management')
                ->assertSee('Total Users')
                ->assertSee('3')
                ->assertSee('Total Tasks');
        });
    }

    public function test_can_view_user_details(): void
    {
        $user = User::factory()->create(['name' => 'Jane Smith', 'email' => 'jane@example.com']);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/users')
                ->assertSee('Jane Smith')
                ->click("@user-{$user->id}")
                ->assertPathIs("/users/{$user->id}")
                ->assertSee('Jane Smith')
                ->assertSee('jane@example.com')
                ->assertSee('Tasks');
        });
    }
}


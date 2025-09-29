<?php

declare(strict_types=1);

use App\Models\User;

it('an unauthenticated user can login', function (): void {
    $user = User::factory()->create([
        'email' => 'gvieira18@sycorax.com',
        'password' => 'password',
    ]);

    visit('/admin/login')
        ->fill('form.email', $user->email)
        ->fill('form.password', 'password')
        ->submit()
        ->assertSee('Dashboard');

    $this->assertAuthenticated();
})->skip();

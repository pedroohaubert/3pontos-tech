<?php

declare(strict_types=1);

it('the application returns a successful response', function (): void {
    visit('/')
        ->assertSee('Reddit')
        ->assertNoSmoke();
})->skip();

<?php

declare(strict_types=1);

test('the application renders the welcome page layout', function (): void {
    $response = $this->get('/');

    $response->assertOk();
    $response->assertViewIs('welcome');
    $response->assertSee('<header class="sticky top-0">', false);
    $response->assertSee('<aside>', false);
});

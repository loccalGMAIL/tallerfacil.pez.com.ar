<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_redirects_to_login_when_unauthenticated(): void
    {
        $response = $this->get('/');

        // La ruta raíz requiere autenticación, redirige a login
        $response->assertRedirect('/login');
    }
}

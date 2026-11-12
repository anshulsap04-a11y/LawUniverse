<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the public home landing page renders cleanly.
     */
    public function test_the_public_home_renders_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('LAW UNIVERSE', false);
        $response->assertSee('Bare Acts');
        $response->assertSee('Legal Maxims');
    }
}

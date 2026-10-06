<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Rădăcina trimite spre limba implicită, de unde se servește pagina de intrare.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $this->withHeader('Accept-Language', 'ro')
            ->get('/')
            ->assertRedirect('/ro');

        $this->get('/ro')->assertStatus(200);
        $this->get('/en')->assertStatus(200);
    }
}

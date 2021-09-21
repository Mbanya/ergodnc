<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TagsControllerTest extends TestCase
{
    /**
     *
     * @test
     *
     */

    public function itListsTags(): void
    {
        $response = $this->get('/api/tags');
        $response->assertOk();
        $this->assertNotNull($response->json('data')[0]['id']);
    }

}

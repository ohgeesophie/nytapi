<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Http;

class BestSellersControllerTest extends TestCase
{
    /**
     * Base GET
     *
     * @return void
     */
    public function testApiGet()
    {
        $response = $this->get('/api/1/nyt/best-sellers');
        $response->assertStatus(200);
    }
}

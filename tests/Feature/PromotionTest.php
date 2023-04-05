<?php

namespace Tests\Feature;

use App\Models\Promotion;
use Database\Factories\FileFactory;
use Database\Factories\PromotionFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PromotionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        FileFactory::new()->count(1)->create();
        PromotionFactory::new()->count(30)->create();
    }

    /**
     * Test general api call
     */
    public function test_can_get_promotion_list_with_proper_structure(): void
    {
        $this->get('/api/v1/main/promotions')
        ->assertStatus(200)
        ->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => [
                    'uuid',
                    'title',
                    'content',
                    'metadata' => [
                        'image',
                        'valid_to',
                        'valid_from',
                    ],
                    'created_at',
                    'updated_at',
                ],
            ],
            'first_page_url',
            'from',
            'last_page',
            'last_page_url',
            'links' => [
                '*' => [
                    'url',
                    'label',
                    'active',
                ],
            ],
            'next_page_url',
            'path',
            'per_page',
            'prev_page_url',
            'to',
            'total',
        ])
        ->assertJsonFragment([
            'current_page' => 1,
            'per_page' => 10,
            'to' => 10,
            'total' => 30,
        ]);
    }

    /**
     * Test with pagination request
     *
     * @return void
     */
    public function test_promotion_list_pagination(): void
    {
        $this->get('/api/v1/main/promotions?page=4&limit=2')
        ->assertStatus(200)
        ->assertJsonFragment([
            'current_page' => 4,
            'per_page' => 2,
            'to' => 8,
            'total' => 30,
        ]);
    }

    /**
     * Test filters if data match
     *
     * @return void
     */
    public function test_promotion_list_filters(): void
    {
        $promotions = Promotion::active(true)
        ->sort('title', 'true')
        ->paginate(10);

        $response = $this->get('/api/v1/main/promotions?sortBy=title&desc=true&valid=true');

        $dbData = collect($promotions)->toArray()['data'];
        $responseData = $response['data'];

        $response->assertStatus(200);

        $this->assertEquals($responseData, $dbData);
    }
}

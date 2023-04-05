<?php

namespace Tests\Feature;


use Tests\TestCase;
use App\Models\Post;
use App\Exceptions\V1\ApiHandler;
use Database\Factories\FileFactory;
use Database\Factories\PostFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        FileFactory::new()->count(1)->create();
        PostFactory::new()->count(30)->create();
    }

    /**
     * Test general api call
     */
    public function test_can_get_post_list_with_proper_structure(): void
    {
        $this->get('/api/v1/main/blog')
        ->assertStatus(200)
        ->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => [
                    'uuid',
                    'title',
                    'slug',
                    'content',
                    'metadata' => [
                        'image',
                        'author',
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
    public function test_post_list_pagination(): void
    {
        $this->get('/api/v1/main/blog?page=4&limit=2')
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
    public function test_post_list_filters(): void
    {
        $posts = Post::sort('title', 'true')->paginate(10);

        $response = $this->get('/api/v1/main/blog?sortBy=title&desc=true');

        $dbData = collect($posts)->toArray()['data'];
        $responseData = $response['data'];

        $response->assertStatus(200);

        $this->assertEquals($responseData, $dbData);
    }

    /**
     * Test if data match
     *
     * @return void
     */
    public function test_can_get_single_post(): void
    {
        $post = Post::all()->random();
        $postUuid = $post->uuid;

        $response = $this->get("/api/v1/main/blog/{$postUuid}");

        $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data' => [
                'uuid',
                'title',
                'slug',
                'content',
                'metadata' => [
                    'image',
                    'author',
                ],
                'created_at',
                'updated_at'
            ]
        ])
        ->assertJsonFragment([
            'success' => 1,
            'uuid' => $post->uuid,
            'title' => $post->title,
        ]);
    }

    /**
     * Test if error occurs on non existing post
     *
     * @return void
     */
    public function test_cannot_get_non_existing_single_post(): void
    {
        $this->expectException(ApiHandler::class);
        $response = $this->get("/api/v1/main/blog/123-2131-33");
        $response->assertStatus(404)
        ->assertJsonStructure([
            'success',
            'data',
            'error',
            'errors',
            'trace',
        ])
        ->assertJsonFragment([
            'success' => 0,
            'error' => 'Post not found',
        ]);
    }
}

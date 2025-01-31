<?php

declare(strict_types=1);

namespace Tests\Feature\Feedbacks;

use App\Models\User\User;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\Providers\FeedbackProvider;

class CreateFeedbackTest extends TestCase
{
    use DatabaseMigrations;

    public string $route;

    /** @test */
    public function hasValidationErrors()
    {
        $response = $this->post($this->route, FeedbackProvider::invalidPayload());
        $response->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->assertEquals(
            array_keys($response->response->json()),
            array_keys(FeedbackProvider::payload())
        );

        $response = $this->post($this->route, FeedbackProvider::payload(-1, 1));
        $response->assertResponseStatus(Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function botCanCreateFeedback()
    {
        /**
         * @var User $sender
         * @var User $target
         */
        [$sender, $target] = User::factory()->count(2)->create();

        $response = $this->post($this->route, FeedbackProvider::payload($sender->discord_id, $target->discord_id));

        $response->assertResponseStatus(Response::HTTP_CREATED);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->route = route('feedback.create');
    }
}

<?php

namespace Tests\Feature\Users;

use App\Models\User\User;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class DeleteUserTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->artisan('db:seed');
    }

    public function test_bot_can_delete_an_user()
    {
        $user = User::factory()->create();

        $response = $this->delete(
            route('users.destroy', ['discordId' => $user->discord_id]),
            [],
            $this->getHeaders()
        );

        $response->seeStatusCode(Response::HTTP_NO_CONTENT);
        $this->notSeeInDatabase('users', ['id' => $user->getKey()]);
    }

    public function test_bot_without_a_key_should_not_dekete_a_user()
    {
        $payload = ['discord_id' => '13374002922'];
        $response = $this->delete(route('users.destroy', $payload));
        $response->seeStatusCode(Response::HTTP_UNAUTHORIZED);
    }
}

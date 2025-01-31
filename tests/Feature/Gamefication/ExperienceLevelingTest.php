<?php

namespace Tests\Feature\Gamefication;

use App\Models\Gamefication\Season;
use App\Models\User\User;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpFoundation\Response;
use Tests\Providers\User\MessageProvider;
use Tests\TestCase;

class ExperienceLevelingTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->artisan('db:seed');
        $season = Season::factory()->activeSeason()->create();
        config(['he4rt.season.id' => $season->getKey()]);
        config(['gambling.xp.message' => 5]);
    }

    public function test_user_can_level_up()
    {
        $user = User::factory()->create([
            'level' => 1,
            'current_exp' => 9
        ]);

        $this->post(route('users.messages.store', ['discordId' => $user->discord_id]),
            MessageProvider::validMessage(),
            $this->getHeaders()
        )->seeStatusCode(Response::HTTP_NO_CONTENT);


        $this->seeInDatabase('users', [
            'id' => $user->getKey(),
            'level' => 2
        ]);

        $this->notSeeInDatabase('users', [
            'id' => $user->getKey(),
            'current_exp' => 9
        ]);
    }

    public function test_user_can_gain_experience()
    {
        $user = User::factory()->create(['current_exp' => 0, 'level' => 1]);
        $response = $this->post(route('users.messages.store', ['discordId' => $user->discord_id]),
            MessageProvider::validMessage(),
            $this->getHeaders()
        );

        $response->seeStatusCode(Response::HTTP_NO_CONTENT);
        $this->notSeeInDatabase('users', [
            'id' => $user->getKey(),
            'current_exp' => 0
        ]);
    }

    public function test_user_should_not_gain_experience()
    {
        $user = User::factory()->create(['current_exp' => 0, 'level' => 1]);
        $response = $this->post(route('users.messages.store', ['discordId' => $user->discord_id]),
            MessageProvider::validMessageWithInvalidChannel(),
            $this->getHeaders()
        );

        $response->seeStatusCode(Response::HTTP_NO_CONTENT);
        $this->seeInDatabase('users', [
            'id' => $user->getKey(),
            'current_exp' => 0
        ]);
    }
}

<?php

namespace Tests\Feature\Events\Meetings;

use App\Models\Events\MeetingType;
use App\Models\User\User;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CreateMeetingTest extends TestCase
{
    use DatabaseMigrations;

    public function testBotCanStartNewMeeting(): void
    {
        // Arrange
        $user = User::factory()->create();
        $meetingType = MeetingType::factory()->create();
        $payload = [
            'meeting_type_id' => $meetingType->getKey(),
            'discord_id' => $user->discord_id
        ];
        $expectedResponse = [
            'admin_id' => $user->getKey(),
            'meeting_type_id' => $meetingType->getKey()
        ];

        // Act
        $response = $this->post(route('events.meeting.postMeeting'), $payload, $this->getHeaders());

        // Assert
        $response->seeStatusCode(Response::HTTP_CREATED)->seeJson($expectedResponse);
        $this->seeInDatabase('meetings', $expectedResponse);
    }
}

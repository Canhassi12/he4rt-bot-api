<?php

namespace Tests\Providers\User;

use Carbon\Carbon;

class MessageProvider
{

    public static function validMessage(): array
    {
        return [
            'channel_id' => 'canal_foda_123',
            'message_id' => 'id_foda_123',
            'message_content' => 'deixa o sub namoral',
            'message_at' => Carbon::now(),
        ];
    }

    public static function validMessageWithInvalidChannel(): array
    {
        return [
            'channel_id' => config('he4rt.channels.commands'),
            'message_id' => 'id_foda_123',
            'message_content' => 'deixa o sub namoral',
            'message_at' => Carbon::now(),
        ];
    }
}

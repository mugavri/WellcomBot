<?php

require_once 'settings.php';
require_once 'functions.php';
require_once 'token.php';

$update = json_decode(file_get_contents('php://input'), true);


if (!isset($update['chat_member']) && !isset($update['callback_query']) && !isset($update['message'])) {
    exit(1);
}

// _log('update',$update);

if (isset($update['chat_member'])) {
    $chat_id = $update['chat_member']['chat']['id'];
    $user_id = $update['chat_member']['from']['id'];

    $if_new = $update['chat_member']['new_chat_member']['status'] == 'member';

    if ($if_new) {

        $user_name = $update['chat_member']['from']['first_name'];

        $text = 'שלום ' . $user_name . ' וברוכים הבאים לקבוצה!';

        $keyboard = json_encode([
            'inline_keyboard' => [
                [
                    [
                        'callback_data' => 'verify ' . $user_id,
                        'text' => "אמת את זהותך"
                    ]
                ]
            ]
        ]);

        $post_data = [
            'chat_id' => $chat_id,
            'text' => $text,
            'reply_markup' => $keyboard
        ];

        telegram('sendMessage', $post_data);

        $post_data = [
            'chat_id' => $chat_id,
            'user_id' => $user_id,
            'permissions' => json_encode([
                'can_send_messages' => false
            ])
        ];

        telegram('restrictChatMember', $post_data);
    }
} else if (isset($update['message'])) {
    if (isset($update['message']['new_chat_members'])) {
        $post_data = [
            'chat_id' => $update['message']['chat']['id'],
            'mesasge_id' => $update['message']['message_id']
        ];

        telegram('deleteMessage', $post_data);
    }
} else if (isset($update['callback_query'])) {

    if (preg_match('/(^verify)/', $update['callback_query']['data'])) {
        $user_id_to_verifaied = intval(explode(' ', $update['callback_query']['data'])[1]);

        $chat_id = $update['callback_query']['message']['chat']['id'];
        $user_id_press = $update['callback_query']['from']['id'];

        if ($user_id_to_verifaied == $user_id_press) {

            $post_data = [
                'chat_id' => $chat_id,
            ];

            $chat_data = telegram('getChat', $post_data);

            $post_data = [
                'chat_id' => $chat_id,
                'user_id' => $user_id_to_verifaied,
                'permissions' => json_encode($chat_data['result']['permissions'])
            ];

            telegram('restrictChatMember', $post_data);

            $post_data = array(
                'callback_query_id' => $update["callback_query"]['id'],
                'text' => "זהותך אושרה בהצלחה, המשך שימוש מהנה בקבוצה.",
                'show_alert' => true
            );

            telegram('answerCallbackQuery', $post_data);

            $post_data = [
                'chat_id' => $chat_id,
                'message_id' => $update['callback_query']['message']['message_id'],
            ];

            telegram('deletemessage', $post_data);
        } else {
            $post_data = array(
                'callback_query_id' => $update["callback_query"]['id'],
                'text' => "אתה לא יכול לאמת זהות של מישהו אחר",
                'show_alert' => true
            );

            telegram('answerCallbackQuery', $post_data);
        }
    }
}

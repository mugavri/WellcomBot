<?php

require_once 'settings.php';
require_once 'functions.php';
require_once 'token.php';

$update = json_decode(file_get_contents('php://input'), true);


if (!isset($update['chat_member'])) {
    exit(1);
}

$chat_id = $update['chat_member']['chat']['id'];
$user_id = $update['chat_member']['from']['id'];

$if_new = isset($update['chat_member']['new_chat_member']['status']['member']);

if ($if_new) {
    $user_name = $update['chat_member']['from']['first_name'];

    $text = 'שלום ' . $user_name . ' וברוכים הבאים לקבוצה!';

    $psot_data = [
        'chat_id' => $chat_id,
        'text' => $text
    ];

    telegram('sendMessage', $psot_data);
}

<?php

function readData()
{
    return file_get_contents('data.json');
}

function writeData($data)
{
    return file_put_contents('data.json', $data);
}

function telegram($method, $datas = [], $header = null)
{
    $url = "https://api.telegram.org/bot" . BOT_TOKEN . "/" . $method;

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);

    if ($header != null) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    }

    $res = curl_exec($ch);
    if (curl_error($ch)) {
        var_dump(curl_error($ch));
        curl_close($ch);
        exit(1);
    } else {
        curl_close($ch);
        return json_decode($res, true);
    }
}

function _log($t = "log_here", $obj = null, $s = false)
{
    if ($s) {
        _log(debug_backtrace(), null, false);
    }

    if ($obj != null && is_string($t)) {
        if (!is_string($obj)) {
            $obj = json_encode($obj, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        }
        $t = $t . ":\n" . $obj;
    }

    if (!is_string($t)) {
        $t = json_encode($t, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    usleep(25000);

    $texts = mb_str_split($t, 4096);

    foreach ($texts as $text) {

        $postData = array(
            'chat_id' => ADMIN_ID,
            'text' => $text
        );

        $a = telegram('sendMessage', $postData);
    }

    return $a;
}

<?php
// TODO: move to env file
const TELEGRAM_BOT_TOKEN = 'XXXXX:XXXXX'; // you can create new bot and generate this token with @BotFather bot - write there /newbot command
const TELEGRAM_BOT_ADMIN_CHAT_ID = 'XXXXX'; // your chat id, you can find it with @cid_bot bot
// don't forget to set up webhook for telegram bot
const API_PORT = 'PORT';
const API_URL = 'https://XXXXX:PORT/SOMEKEYFROMOUTLINE/';

function sendToTelegram(array $args, $url): void
{

    $curl = curl_init();

    $urlWithName = $url . urlencode('Name of your server for Outline VPN Client');

    $id = $args['message']['chat']['id'];
    $lang = $args['message']['from']['language_code'] ?? 'en';

    //if ($lang === 'en') {
    $message = 'Hello! I invite you to connect to my VPN (Outline) serverm so you will get access to the free Internet, wherever you are. Follow the instructions on the link provided in the invitation to download the app and set up your connection.

https://s3.amazonaws.com/outline-vpn/invite.html#' . $url . ' 

Have problems accessing the invitation link? Write to: @flatroy


Click and copy an access key: `' . $urlWithName . '`
';
//    } else {
    // with this IF you can add some message in different language if you want
    //  }

    $message = urlencode($message);
    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://api.telegram.org/bot'.TELEGRAM_BOT_TOKEN.'/sendMessage?chat_id=' . $id . '&parse_mode=Markdown&text=' . $message,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_POSTFIELDS => "",
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    // send message to admin
    $curl = curl_init();

    $id = $args['message']['chat']['id'];


    // notify admin of bot
    $message = 'new user message: ' . $args['message']['text'] . " from @" . $args['message']['chat']['username'] . ' ' . $args['message']['chat']['id'] . ' ' . $args['message']['chat']['first_name'] . ' - lang: ' . $args['message']['from']['language_code'];

    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://api.telegram.org/bot'.TELEGRAM_BOT_TOKEN.'/sendMessage?chat_id='.TELEGRAM_BOT_ADMIN_CHAT_ID.'&text=' . $message,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_POSTFIELDS => "",
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);
}


function main(array $args): array
{
    $API_PORT = API_PORT;
    $API_URL = API_URL;


    if ($args['message']['text'] === '/create' || $args['message']['text'] === '/start') {

        //check that if we already have key
        $name = "@" . $args['message']['chat']['username'] . ' ' . $args['message']['chat']['id'] . ' ' . $args['message']['chat']['first_name'] . ' - from My Bot';

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_PORT => $API_PORT,
            CURLOPT_URL => $API_URL . '/access-keys/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return ["body" => "cURL Error #:" . $err];
        } else {

            $keys = json_decode($response, true);
            foreach ($keys['accessKeys'] as $key) {

                if ($key['name'] === $name) {
                    //return ["body" => 'ok exists'];

                    sendToTelegram($args, $key['accessUrl']);
                    return ["body" => 'ok'];
                }
            }
        }

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_PORT => $API_PORT,
            CURLOPT_URL => $API_URL . '/access-keys/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return ["body" => "cURL Error #:" . $err];

        }

        echo $response;
        $data = json_decode($response, true);
        sendToTelegram($args, $data['accessUrl']);

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_PORT => $API_PORT,
            CURLOPT_URL => $API_URL . '/access-keys/' . $data['id'] . '/name',
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS => "name=" . $name,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/x-www-form-urlencoded"
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 30,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);


        // limit traffic

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_PORT => $API_PORT,
            CURLOPT_URL => $API_URL . '/access-keys/' . $data['id'] . '/data-limit',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS => "{\"limit\":{\n\t\"bytes\": 200000000\n}}",
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            echo $response;
        }

    }
    return ["body" => 'ok'];
}

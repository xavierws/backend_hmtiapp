<?php


namespace App\Actions;


class PushNotification
{
    public static function handle ($title, $description)
    {
        $data = [
            "to" => "/topics/event",
            "notification" =>
                [
                    "title" => $title,
                    "body" => $description
                ],
        ];
        $dataString = json_encode($data);

        $headers = [
            'Authorization: key=AAAA7DnAwoc:APA91bEiqEGplmavyMQZzT4iqmU-RDpmGyE6CLYr31aBWsiLGRZymQlZhyqbeNyPfJyt-Uqxi0TXgrm-TPCkDYMFMvcMArpw-2s5pwet0IrbP_4ayyJdk5JYjJg24SFXsIf6BQro0r66',
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

        curl_exec($ch);
    }
}

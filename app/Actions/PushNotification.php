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
            'Authorization: key=' . env('FCM_AUTH_KEY'),
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

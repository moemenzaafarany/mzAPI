<?php
/* 1.0.0 */
class mzFirebase
{
    //===============================================================================//
    public $server_key = "";

    //===============================================================================//
    public function __construct(String $server_key)
    {
        $this->server_key = $server_key;
    }

    //===============================================================================//
    function checkPushNotificationToken(string $token): object
    {
        try {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://iid.googleapis.com/iid/info/' . $token,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: ' . $this->server_key,
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            return mzAPI::return(200, null, null, $response);
        } catch (Exception $e) {
            return mzAPI::return(500, $e);
        }
    }

    //===============================================================================//
    function sendPushNotification(
        array $tokens,
        string $message_group = "",
        string $message_priority = "normal", // noraml , high,
        string $message_title = "",
        string $message_body = "",
        string $message_icon = "",
        string $message_image = "",
        array $data = []
    ): object {
        try {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://fcm.googleapis.com/fcm/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode([
                    'registration_ids' => $tokens,
                    'collapse_key' => $message_group,
                    'priority' => $message_priority,
                    'content_available' => false,
                    'notification' => [
                        'tag' => $message_group,
                        'title' => $message_title,
                        'body' => $message_body,
                        'icon' => $message_icon,
                        'image' => $message_image,
                        'sound' => 'default'
                    ],
                    'data' => $data
                ]),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: key=' . $this->server_key,
                    'Content-Type: application/json'
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            return mzAPI::return(200, null, null, $response);
        } catch (Exception $e) {
            return mzAPI::return(500, $e);
        }
    }

    //===============================================================================//
    //===============================================================================//
}

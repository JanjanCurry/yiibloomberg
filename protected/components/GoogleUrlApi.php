<?php class GoogleUrlApi extends CComponent {

    public static function shorten ($url) {
        $response = self::send($url);
        return isset($response['id']) ? $response['id'] : false;
    }

    public static function expand ($url) {
        $response = self::send($url, false);
        return isset($response['longUrl']) ? $response['longUrl'] : false;
    }

    // Send information to Google
    private static function send ($url, $shorten = true) {
        $apiUrl = 'https://www.googleapis.com/urlshortener/v1/url?key=' . Yii::app()->params['googleApiKey'];
        // Create cURL
        $ch = curl_init();
        // If we're shortening a URL...
        if ($shorten) {
            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array("longUrl" => $url)));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
        } else {
            curl_setopt($ch, CURLOPT_URL, $apiUrl . '&shortUrl=' . $url);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // Execute the post
        $result = curl_exec($ch);
        // Close the connection
        curl_close($ch);
        var_dump($result);
        // Return the result
        return json_decode($result, true);
    }
}
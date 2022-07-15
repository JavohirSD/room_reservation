<?php

namespace Models\Helpers;

trait Email
{
    /**
     * @param string $to email address of receiver
     * @param string $subject message subject header
     * @param string $body message content text with html tags support
     *  Sending email to users using open API
     * @return mixed
     */
    public function sendEmail(string $to, string $subject, string $body)
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => 'https://javohirs.uz/api/v1/email/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => '{
                "to" : "' . $to . '",
                "subject" : "' . $subject . '",
                "body": "' . $body . '"
            }',
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer 78Jknr2Ea4CeJbVgtto73JVpEkqMmjd9',
                'Content-Type: application/json',
            ],
        ]);

        $response = json_decode(curl_exec($curl));
        curl_close($curl);
        return $response;
    }
}
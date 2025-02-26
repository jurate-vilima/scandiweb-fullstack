<?php

namespace App\Controller;

error_reporting(E_ALL);
ini_set('display_errors', 1);

class Main {
    public function main() {
        echo 'main';
        $url = 'http://scandiweb-store/graphql'; 
        $query = '{
            products {
                name
            }
        }';

        $data = json_encode(['query' => $query]);

        $ch = curl_init($url);
        if ($ch === false) {
            echo "curl_init failed\n";
            return;
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data)
        ]);

        $response = curl_exec($ch);
        //$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch) . "\n";
        } else {
            // echo "Raw response:\n<<<START>>>\n" . $response . "\n<<<END>>>\n";
            // var_dump($response);
        }

        curl_close($ch);

        if ($response === false) {
            echo "No response received from cURL.\n";
            return;
        }

        // $result = json_decode($response, true);
       
        // if (json_last_error() !== JSON_ERROR_NONE) {
        //     echo "JSON decode error: " . json_last_error_msg() . "\n";
        //     return;
        // }

        if (isset($result['error'])) {
            echo "GraphQL Error: " . print_r($result['error'], true) . "\n";
        } else {
            // echo "Final result:\n";
            print "<pre>";
            var_dump($response);
            print "</pre>";
        }
    }
}

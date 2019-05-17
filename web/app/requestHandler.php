<?php

class requestHandler
{
    private $proxies_json = null;
    private $proxies = null;
    public  $current_country = null;
    private $proxy = null;
    private $failed_requests_counter = 0;

    public function __construct()
    {
        $this->proxies_json = file_get_contents('proxies.json');
        $this->proxies = json_decode($this->proxies_json, JSON_UNESCAPED_UNICODE);
    }

    public function makeRequest($country, $request_page, $request_users_size, $log_file) {
        $request = "http://website";
        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $request);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($handle, CURLOPT_HTTPHEADER, array('user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.103 Safari/537.36','Content-type: text/html; charset=UTF-8'));
        // Tell cURL that it should only spend 30 seconds trying to connect to the URL in question.
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 30);
        // A given cURL operation should only take 210 seconds max.
        curl_setopt($handle, CURLOPT_TIMEOUT, 180);
        //     Setting proxy option for cURL
        if(isset($this->proxies)) {
            $proxy_rand_id = array_rand($this->proxies);
            $this->proxy = $this->proxies[$proxy_rand_id];
        }
        if(isset($this->proxy['ip'])) {    // If the $proxy variable is set, then
            curl_setopt($handle, CURLOPT_PROXY, $this->proxy['ip']);    // Set CURLOPT_PROXY with proxy in $proxy variable
            fwrite($log_file,  time() . " Proxy country = " . $this->proxy['country'] . " proxy ip = " . $this->proxy['ip'] . PHP_EOL);
        }
        fwrite($log_file, time() . " Make request. Country = " . $country['name']. " Request_page = " . $request_page . " request_users_size = " . $request_users_size . PHP_EOL);
        $response_json = curl_exec($handle);
        curl_close($handle);
        $response = json_decode($response_json, JSON_UNESCAPED_UNICODE);
        if($this->failed_requests_counter >= 15) {
            echo("Failed Requests Count >= 15");
            fwrite($log_file, time() . " Failed count is >= 15" .  PHP_EOL);
            fclose($log_file);
            die();
        }
        if(!$response && $this->failed_requests_counter < 15) {
            $this->failed_requests_counter++;
            fwrite($log_file, time() . " Not Successful request for " . $country['name'] . ", count is " . $this->failed_requests_counter .  PHP_EOL );
            fwrite($log_file, time() . $response .  PHP_EOL );
//            $response = $this->makeRequest($country, $request_page, $request_users_size, $log_file);
            return false;
        } else {
            $this->failed_requests_counter = 0;
            return $response;
        }
    }

}
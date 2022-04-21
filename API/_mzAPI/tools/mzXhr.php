<?php
/* 1.0.0 */
class mzXhr
{
    //====================================//
    public ?string $method;
    public ?string $url;
    public ?array $headers;
    public ?array $fields;

    //====================================//
    public function __construct(String $method, string $url, array $headers = null, array $fields = null)
    {
        $this->method = $method;
        $this->url = $url;
        $this->headers = $headers;
        $this->fields = $fields;
    }
    //====================================//
    public function send()
    {
        try {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_NOBODY, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_URL, $this->url);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $this->method);
            if (!empty($this->headers)) curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
            if (!empty($this->data)) curl_setopt($curl, CURLOPT_POSTFIELDS, $this->data);
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $error = curl_error($curl) ?: null;
            curl_close($curl);
            return new mzRes($httpCode, $error, null, $response);
        } catch (Exception $e) {
            return new mzRes(500, $e);
        }
    }
    //====================================//
}

<?php

define("API2PDF_BASE_ENDPOINT", 'https://v2018.api2pdf.com');
define("API2PDF_MERGE", API2PDF_BASE_ENDPOINT.'/merge');
define("API2PDF_WKHTMLTOPDF_HTML", API2PDF_BASE_ENDPOINT.'/wkhtmltopdf/html');
define("API2PDF_WKHTMLTOPDF_URL", API2PDF_BASE_ENDPOINT.'/wkhtmltopdf/url');
define("API2PDF_CHROME_HTML", API2PDF_BASE_ENDPOINT.'/chrome/html');
define("API2PDF_CHROME_URL", API2PDF_BASE_ENDPOINT.'/chrome/url');
define("API2PDF_LIBREOFFICE_CONVERT", API2PDF_BASE_ENDPOINT.'/libreoffice/convert');

class Api2Pdf_Library {
    var $apikey;
    function __construct($apikey) {
        $this->apikey = $apikey;    
    }
    public function api2pdf_headless_chrome_from_url($url, $inline = false, $filename = null, $options = null) {
        $payload = array("url"=>$url, "inlinePdf"=>$inline);
        if ($filename != null) {
            $payload["fileName"] = $filename;
        }
        if ($options != null) {
            $payload["options"] = $options;
        }
        return $this->api2pdf_make_request(API2PDF_CHROME_URL, $payload);
    }
    public function api2pdf_headless_chrome_from_html($html, $inline = false, $filename = null, $options = null) {
        $payload = array("html"=>$html, "inlinePdf"=>$inline);
        if ($filename != null) {
            $payload["fileName"] = $filename;
        }
        if ($options != null) {
            $payload["options"] = $options;
        }
        return $this->api2pdf_make_request(API2PDF_CHROME_HTML, $payload);
    }
    public function api2pdf_wkhtmltopdf_from_url($url, $inline = false, $filename = null, $options = null) {
        $payload = array("url"=>$url, "inlinePdf"=>$inline);
        if ($filename != null) {
            $payload["fileName"] = $filename;
        }
        if ($options != null) {
            $payload["options"] = $options;
        }
        return $this->api2pdf_make_request(API2PDF_WKHTMLTOPDF_URL, $payload);
    }
    public function api2pdf_wkhtmltopdf_from_html($html, $inline = false, $filename = null, $options = null) {
        $payload = array("html"=>$html, "inlinePdf"=>$inline);
        if ($filename != null) {
            $payload["fileName"] = $filename;
        }
        if ($options != null) {
            $payload["options"] = $options;
        }
        return $this->api2pdf_make_request(API2PDF_WKHTMLTOPDF_HTML, $payload);
    }
    public function api2pdf_merge($urls, $inline = false, $filename = null) {
        $payload = array("urls"=>$urls, "inlinePdf"=>$inline);
        if ($filename != null) {
            $payload["fileName"] = $filename;
        }
        return $this->api2pdf_make_request(API2PDF_MERGE, $payload);
    }
    public function api2pdf_libreoffice_convert($url, $inline = false, $filename = null) {
        $payload = array("url"=>$url, "inlinePdf"=>$inline);
        if ($filename != null) {
            $payload["fileName"] = $filename;
        }
        return $this->api2pdf_make_request(API2PDF_LIBREOFFICE_CONVERT, $payload);
    }

    private function api2pdf_make_request($endpoint, $payload) {
		$headers = array( 
		'Authorization' => $this->apikey,
		'Content-Type' => 'application/json',
		);
		
		$jsonDataEncoded =  json_encode($payload);
		$pload = array(
			'method' => 'POST',
			'headers' => $headers,
			'body' => $jsonDataEncoded
		);
	
		$response = wp_remote_post($endpoint, $pload);
		$response_body = wp_remote_retrieve_body($response);
		
        if($response_body === false) {
            throw new \Exception(curl_error($ch), 1);
        }
        $result = json_decode($response_body);
        return $result;
    }
}
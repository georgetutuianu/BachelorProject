<?php
/**
 * Handles the http connection
 *
 * @author George Tutuianu <george.tzutzu@gmail.com>
 */
class Utility_Model_HttpHandler
{
    /**
     * Default headers to send the request with
     * 
     * @var array
     */
    private $_defaultHeaders = array(
        'User-Agent' => 'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:30.0) Gecko/20100101 Firefox/30.0'
    );
    
    /**
     * Variable to check if the previous request failed or not
     * 
     * @var bool
     */
    private $_failedRequest = false;
    
    /**
     * Handler of http requests
     * 
     * @var Zend_Http_Client
     */
    private $_handler = null;

    /**
     * Response of the last request
     * 
     * @var Zend_Http_Response_Stream
     */
    private $_lastRequestReponse = null;

    /**
     * The method to send the request (POST, GET, PUT, etc)
     * 
     * @var string
     */
    private $_requestMethod = null;

    /**
     * Instance of class. (Singleton)
     * 
     * @var Utility_Model_HttpHandler
     */
    private static $_instance = null;

    /**
     * Get the content of the current page
     *
     * @param string $url Url where to get the content from
     * @return string | null
     */
    public function getContent($url)
    {
        $this->_sendRequest($url);
        
        // check if the las request has failed to don`t try and get the body for a failure
        if ($this->_failedRequest) {
            return null;
        }
        // return the body of the last request
        return $this->_lastRequestReponse->getBody();
    }
    
    /**
     * Get the instance of the singleton design pattern class
     *
     * @return Utility_Model_HttpHandler
     */
    public static function getInstance()
    {
        // check if there has been another instance to not instantiate it again
        if (is_null(self::$_instance)) {
            // create a new instance of the current class
            self::$_instance = new Utility_Model_HttpHandler();
        }

        return self::$_instance;
    }

    /**
     * Initialization of the class
     */
    private function __construct()
    {
        // create a new handler for http using zend_http_cient
        $this->_handler = new Zend_Http_Client();
        // create a new adapter using curl
        $curlAdapter = new Zend_Http_Client_Adapter_Curl();
        // set the adapter for http client (curl)
        $this->_handler->setAdapter($curlAdapter);
        // set the main reguest method to be used by default
        $this->_requestMethod = Zend_Http_Client::GET;
    }
    
    /**
     * Send the request for the following url
     * 
     * @param string $url Url to send request to
     */
    private function _sendRequest($url)
    {
        // send the http request using setted params (url, headers, method)
        $this->_handler->setUri($url)
                       ->setHeaders($this->_defaultHeaders)
                       ->setMethod($this->_requestMethod);
        
        try {
            // get the last request and store it
            $this->_lastRequestReponse = $this->_handler->request();
        } catch (Exception $exception) {
            // mark when the last request failed
            $this->_failedRequest = true;
            // @TODO log the failed request
        }
    }
}

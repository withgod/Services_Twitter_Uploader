<?php
/**
 * An abstract interface for OAuthUploader Services
 *
 * PHP version 5.2.0+
 *
 * Copyright 2010 withgod
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @category Services
 * @package  Services_Twitter_Uploader
 * @author   withgod <noname@withgod.jp>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache License
 * @version  GIT: $Id$
 * @link     https://github.com/withgod/Services_Twitter_Uploader
 */

require_once 'HTTP/Request2.php';
require_once 'HTTP/OAuth/Consumer.php';
require_once 'Services/Twitter/Uploader/Exception.php';

/**
 * An abstract interface for OAuthUploader Services
 *
 * @category Services
 * @package  Services_Twitter_Uploader
 * @author   withgod <noname@withgod.jp>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache License
 * @version  Release: @package_version@
 * @link     https://github.com/withgod/Services_Twitter_Uploader
 * @see      HTTP_Request2
 * @see      HTTP_OAuth_Consumer
 */
abstract class Services_Twitter_Uploader
{
    const TWITTER_VERIFY_CREDENTIALS_JSON = "https://api.twitter.com/1/account/verify_credentials.json";
    const TWITTER_VERIFY_CREDENTIALS_XML  = "https://api.twitter.com/1/account/verify_credentials.xml";


    /**
     * List of supported services
     * @var array $services
     */
    static protected $services = array(
        'imgly',
        'plixi',
        'twipple',
        'twitgoo',
        'twitpic',
        'yfrog',
        'mobypicture',
        'twipl',
        'posterous'
    );

    /**
     * service api key(some services required)
     * @var string
     */
    protected $apiKey = null;


    /**
     * @see HTTP_OAuth_Consumer
     * @var HTTP_OAuth_Consumer oauth consumer object
     */
    protected $oauth = null;

    /**
     * @var string upload endpoint
     */
    protected $uploadUrl = null;


    /**
     * @see HTTP_Request2_Exception
     * @var HTTP_Request2_Exception event of upload time exception
     */
    protected $postException = null;

    /**
     * upload file path
     * @var string
     */
    protected $postFile = null;

    /**
     * post message
     * @var string
     */
    protected $postMessage = null;

    /**
     * @see HTTP_Request2
     * @var HTTP_Request2 upload request object
     */
    protected $request = null;

    /**
     * @see HTTP_Request2
     * @var HTTP_Request2 upload last request object
     */
    protected $lastRequest = null;

    /**
     * @see HTTP_Request2_Response
     * @var HTTP_Request2_Response upload response object
     */
    protected $response = null;

    /**
     * Constructor
     *
     * @param HTTP_OAuth_Consumer $oauth   oauth consumer
     * @param string              $apiKey  required for some providers
     * @param HTTP_Request2       $request http request provider
     *
     * @see HTTP_OAuth_Consumer
     * @see HTTP_Request2
     */
    public function __construct(
        HTTP_OAuth_Consumer $oauth = null, $apiKey = null,
        HTTP_Request2 $request = null
    ) {
        $this->oauth = $oauth;
        $this->apiKey = $apiKey;

        if ($request !== null) {
            $this->request = $request;
        } else {
            $this->request = new HTTP_Request2();
            $_ua = 'Services_Twitter_Uploader/' . get_class($this)
                . ' PHP_VERSION/' . PHP_VERSION . ' PHP_OS/' . PHP_OS;
            $this->request->setHeader('User-Agent', $_ua);
        }

        $this->request->setMethod(HTTP_Request2::METHOD_POST);
    }

    /**
     * upload method.
     * do not all provider require apikey do not supported send message.
     *
     * @param string $filePath full path of file to upload to the service
     * @param string $message  message (tweet), only supported by some services
     *
     * @throws {@link Services_Twitter_Uploader_Exception}
     *
     * @return string $mediaUrl a media url
     */
    function upload($filePath = null, $message = null)
    {
        $this->postFile    = $filePath;
        $this->postMessage = $message;
        $this->lastRequest = clone $this->request;
        $this->lastRequest->setUrl($this->uploadUrl);
        $this->preUpload();
        if ($this->uploadUrl == null) {
            throw new Services_Twitter_Uploader_Exception(
                'Incomplete implementation of Services_Twitter_Uploader'
            );
        }

        try {
            $this->response = $this->lastRequest->send();
        } catch (HTTP_Request2_Exception $e) {
            $this->postException = $e;
        } catch (Exception $e) {
            throw new Services_Twitter_Uploader_Exception($e->getMessage());
        }

        $mediaUrl = $this->postUpload();
        //var_dump($mediaUrl);

        if (empty($mediaUrl)) {
            throw new Services_Twitter_Uploader_Exception(
                'Incomplete implementation or not handle Exception'
            );
        }

        return $mediaUrl;
    }

    /**
     * utility method.
     * for developers method. don't use end-user
     *
     * @param string $verify_url Twitter's verify_credentials url.
     *
     * @return array signed parameter and signature array
     * @see    self::TWITTER_VERIFY_CREDENTIALS_JSON
     * @see    self::TWITTER_VERIFY_CREDENTIALS_XML
     */
    protected function buildSignature($verify_url)
    {
        $signature = HTTP_OAuth_Signature::factory(
            $this->oauth->getSignatureMethod()
        );
        $params = array(
            'oauth_consumer_key'     => $this->oauth->getKey(),
            'oauth_signature_method' => $this->oauth->getSignatureMethod(),
            'oauth_token'            => $this->oauth->getToken(),
            'oauth_timestamp'        => time(),
            'oauth_nonce'            => md5(microtime(true) . rand(1, 999)),
            'oauth_version'          => '1.0a'
        );

        $params['oauth_signature'] = $signature->build(
            'GET',
            $verify_url,
            $params,
            $this->oauth->getSecret(),
            $this->oauth->getTokenSecret()
        );

        return $params;
    }

    /**
     * utility method.
     *
     * for developers method. don't use end-user
     * if provider is in request based implemention of oauth echo. this method use
     *
     * @param string $verify_url the verification url
     *
     * @return string signed verify_url to url format
     * @uses   self::buildSignature()
     * @uses   HTTP_OAuth::urlencode()
     */
    protected function genVerifyUrl($verify_url)
    {
        $params = $this->buildSignature($verify_url);
        $pairs = array();
        foreach ($params as $k => $v) {
            $pairs[] =  HTTP_OAuth::urlencode($k) . '=' . HTTP_OAuth::urlencode($v);
        }

        return $verify_url . '?' . implode('&', $pairs);

    }

    /**
     * utility method.
     *
     * for developers method. don't use end-user
     * if provider is in request based implementation of oauth echo. this method use
     *
     * @param string $verify_url verification url
     *
     * @return string signed verification url
     * @uses   self::buildSignature()
     * @uses   HTTP_OAuth::urlencode()
     */
    protected function genVerifyHeader($verify_url)
    {
        $params = $this->buildSignature($verify_url);
        $pairs = array();
        foreach ($params as $k => $v) {
            $pairs[] =  HTTP_OAuth::urlencode($k)
                . '="' . HTTP_OAuth::urlencode($v) . '"';
        }

        return 'OAuth realm="http://api.twitter.com/", ' . implode(', ', $pairs);
    }

    /**
     * create uploader instance method.
     *
     * @param string              $serviceName uploader service name
     * @param HTTP_OAuth_Consumer $oauth       oauth consumer instance
     * @param string              $apiKey      apiKey some provider is requred
     * @param HTTP_Request2       $request     optional instance of HTTP_Request2
     *
     * @throws Services_Twitter_Uploader_Exception
     *
     * @return Services_Twitter_Uploader
     * @see    self::$services
     */
    public static function factory(
        $serviceName, HTTP_OAuth_Consumer $oauth,
        $apiKey = null, HTTP_Request2 $request = null
    ) {
        $lc = strtolower($serviceName);
        if (in_array($lc, self::$services)) {
            $uc = ucwords($lc);
            include_once "Services/Twitter/Uploader/{$uc}Uploader.php";
            $class = "Services_Twitter_Uploader_{$uc}Uploader";
            return new $class($oauth, $apiKey,  $request);
        }
        throw new Services_Twitter_Uploader_Exception(
            'unknown service name' . $serviceName . ']'
        );
    }

    /**
     * This method is run in each implementation in from postUpload().
     *
     * @param HTTP_Request2_Response $response The response object.
     * @param int                    $code     The expected response code.
     *
     * @return string The response body.
     *
     * @throws Services_Twitter_Uploader_Exception
     * @throws Services_Twitter_Uploader_Exception When the response code doesn't
     *                                          match what is expected.
     * @uses self::$response
     * @uses self::$postException
     */
    protected function postUploadCheck(
        HTTP_Request2_Response $response = null, $code = 200
    ) {
        if (!empty($this->postException)
            && ($this->postException instanceof Exception)
        ) {
            throw new Services_Twitter_Uploader_Exception(
                $this->postException->getMessage()
            );
        }
        if ($response->getStatus() != $code) {
            throw new Services_Twitter_Uploader_Exception(
                'invalid response status code [' . $response->getStatus() . ']'
            );
        }
        return $response->getBody();
    }

    /**
     * set an instance of HTTP_Request2
     *
     * @param HTTP_Request2 $request HTTP_Request2 implments class
     *
     * @return void
     */
    protected function setRequest(HTTP_Request2 $request)
    {
        $this->request = $request;
        $this->request->setMethod(HTTP_Request2::METHOD_POST);
    }

    /**
     * set an instance of HTTP_OAuth_Consumer
     *
     * @param HTTP_OAuth_Consumer $oauth HTTP_OAuth_Consumer implments class
     *
     * @return void
     */
    protected function setConsumer(HTTP_OAuth_Consumer $oauth)
    {
        $this->oauth = $oauth;
    }

    /**
     * extending classes should implement this method.
     *
     * @return void
     */
    abstract protected function preUpload();

    /**
     * extending classes should implment this method.
     *
     * @return string uploaded url
     */
    abstract protected function postUpload();
}

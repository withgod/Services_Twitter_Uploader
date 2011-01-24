<?php
// vim: ts=4:sw=4:sts=4:ff=unix:fenc=utf-8:et
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
 * @package  Services_OAuthUploader
 * @author   withgod <noname@withgod.jp>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache License
 * @version  Release: @package_version@
 * @link     https://github.com/withgod/Services_OAuthUploader
 */

require_once 'HTTP/Request2.php';
require_once 'HTTP/OAuth/Consumer.php';
require_once 'Services/OAuthUploader/Exception.php';

/**
 * An abstract interface for OAuthUploader Services
 *
 * @category Services
 * @package  Services_OAuthUploader
 * @author   withgod <noname@withgod.jp>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache License
 * @version  Release: @package_version@
 * @link     https://github.com/withgod/Services_OAuthUploader
 * @see      HTTP_Request2
 * @see      HTTP_OAuth_Consumer
 */
abstract class Services_OAuthUploader
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
     * @see HTTP_Request2_Response
     * @var HTTP_Request2_Response upload response object
     */
    protected $response = null;

    /**
     * Constructor
     *
     * @param HTTP_OAuth_Consumer $oauth   oauth consumer
     * @param string              $apiKey  some provider required.
     * @param HTTP_Request2       $request http request provider
     *
     * @see HTTP_OAuth_Consumer
     * @see HTTP_Request2
     */
    function __construct(HTTP_OAuth_Consumer $oauth = null, $apiKey = null, HTTP_Request2 $request = null)
    {
        $this->oauth = $oauth;
        $this->apiKey = $apiKey;

        if ($request !== null) {
            $this->request = $request;
        } else {
            $this->request = new HTTP_Request2();
            $_ua = 'Services_OAuthUploader/' . get_class($this) . ' PHP_VERSION/' . PHP_VERSION . ' PHP_OS/' . PHP_OS;
            $this->request->setHeader('User-Agent', $_ua);
        }

        $this->request->setMethod(HTTP_Request2::METHOD_POST);
    }

    /**
     * upload method.
     * do not all provider require apikey do not supported send message.
     *
     * @param string $filePath path to upload fie
     * @param sting  $message  tweet
     *
     * @throws {@link Services_OAuthUploader_Exception}
     *
     * @return string $mediaUrl a media url
     */
    function upload($filePath = null, $message = null)
    {
        $this->postFile    = $filePath;
        $this->postMessage = $message;
        $this->request->setUrl($this->uploadUrl);
        $this->preUpload();
        if ($this->uploadUrl == null) {
            throw new Services_OAuthUploader_Exception('Incomplete implementation of Services_OAuthUploader');
        }

        try {
            $this->response = $this->request->send();
        } catch (HTTP_Request2_Exception $e) {
            $this->postException = $e;
        } catch (Exception $e) {
            throw new Services_OAuthUploader_Exception($e->getMessage());
        }

        $mediaUrl = $this->postUpload();
        //var_dump($mediaUrl);

        if (empty($mediaUrl)) {
            throw new Services_OAuthUploader_Exception('Incomplete implementation or not handle Exception');
        }

        return $mediaUrl;
    }

    /**
     * utility method.
     *
     * @param string $verify_url verify_url url
     *
     * @return array signed parameter and signature array
     */
    protected function buildSignature($verify_url)
    {
        $signature = HTTP_OAuth_Signature::factory($this->oauth->getSignatureMethod());
        $params = array(
            'oauth_consumer_key'              => $this->oauth->getKey(),
            'oauth_signature_method'          => $this->oauth->getSignatureMethod(),
            'oauth_token'                     => $this->oauth->getToken(),
            'oauth_timestamp'                 => time(),
            'oauth_nonce'                     => md5(microtime(true) . rand(1, 999)),
            'oauth_version'                   => '1.0a'
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
     * if provider is in request based implemention of oauth echo. this method use
     *
     * @param string $verify_url verify_url url
     *
     * @return string signed verify_url to url format
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
     * if provider is in request based implementation of oauth echo. this method use
     *
     * @param string $verify_url verify_url url
     *
     * @return string signed verify_url to request header format
     */
    protected function genVerifyHeader($verify_url)
    {
        $params = $this->buildSignature($verify_url);
        $pairs = array();
        foreach ($params as $k => $v) {
            $pairs[] =  HTTP_OAuth::urlencode($k) . '="' . HTTP_OAuth::urlencode($v) . '"';
        }

        return 'OAuth realm="http://api.twitter.com/", ' . implode(', ', $pairs);
    }

    /**
     * create uploader instance method.
     *
     * @param string              $serviceName uploader service name self::services
     * @param HTTP_OAuth_Consumer $oauth       oauth consumer instance {@link HTTP_OAuth_Consumer}
     * @param string              $apiKey      apiKey some provider is requred
     * @param HTTP_Request2       $request     {@link HTTP_Request2}
     *
     * @throws {@link Services_OAuthUploader_Exception}
     *
     * @return object {@link Services_OAuthUploader}
     */
    public static function factory($serviceName, HTTP_OAuth_Consumer $oauth, $apiKey = null, HTTP_Request2 $request = null)
    {
        $lc = strtolower($serviceName);
        if (in_array($lc, self::$services)) {
            $uc = ucwords($lc);
            include_once "Services/OAuthUploader/{$uc}Uploader.php";
            $clazz = "Services_OAuthUploader_{$uc}Uploader";
            return new $clazz($oauth, $apiKey,  $request);
        } else {
            throw new Services_OAuthUploader_Exception('unknown service name' . $serviceName . ']');
        }
    }

    /**
     * extends classes should implments this method.
     * see other implmention classes
     */
    abstract protected function preUpload();

    /**
     * extends classes should implments this method.
     * see other implmention classes
     *
     * @return string uploaded url
     */
    abstract protected function postUpload();
}

?>

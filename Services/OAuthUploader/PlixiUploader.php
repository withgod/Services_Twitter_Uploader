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
 * @package  Services_OAuthUploader
 * @author   withgod <noname@withgod.jp>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache License
 * @version  Release: @package_version@
 * @link     https://github.com/withgod/Services_OAuthUploader
 */

require_once 'HTTP/Request2.php';
require_once 'Services/OAuthUploader.php';

/**
 * implementation OAuthUploader Services
 *
 * @category Services
 * @package  Services_OAuthUploader
 * @author   withgod <noname@withgod.jp>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache License
 * @version  Release: @package_version@
 * @link     https://github.com/withgod/Services_OAuthUploader
 * @link     http://plixi.com/api
 * @link     https://admin.plixi.com/Api.aspx
 * @see      HTTP_Request2
 */
class Services_OAuthUploader_PlixiUploader extends Services_OAuthUploader
{

    /**
     * upload endpoint
     * @var string
     */
    protected $uploadUrl = "http://tweetphotoapi.com/api/upload.aspx";

    /**
     * Constructor
     *
     * @param HTTP_OAuth_Consumer $oauth   oauth consumer
     * @param string              $apiKey  required
     * @param HTTP_Request2       $request http provider
     *
     * @see HTTP_OAuth_Consumer
     * @see HTTP_Request2
     * @throws Services_OAuthUploader_Exception
     */
    function __construct($oauth = null, $apiKey = null, HTTP_Request2 $request = null)
    {
        parent::__construct($oauth, $apiKey, $request);
        if (empty($apiKey)) {
            throw new Services_OAuthUploader_Exception('PlixiUploader require apiKey');
        }
    }

    /**
     * preUpload implementation
     *
     * @return void
     */
    protected function preUpload()
    {
        $this->request->setConfig('ssl_verify_peer', false);
        $this->request->addPostParameter('api_key', $this->apiKey);
        if (!empty($this->postMessage)) {
            $this->request->addPostParameter('message', $this->postMessage);
        }
        try {
            $this->request->addUpload('media', $this->postFile, basename($this->postFile), 'application/octet-stream');
        } catch (HTTP_Request2_Exception $e) {
            throw new Services_OAuthUploader_Exception('cannot open file ' . $this->postFile);
        }
        $this->request->setHeader(
            array(
                'X-Auth-Service-Provider'            => self::TWITTER_VERIFY_CREDENTIALS_XML,
                'X-Verify-Credentials-Authorization' => $this->genVerifyHeader(self::TWITTER_VERIFY_CREDENTIALS_XML)
            )
        );
    }

    /**
     * postUpload implementation
     *
     * @return string|null image url
     */
    protected function postUpload()
    {
        if (!empty($this->postException)) {
            throw new Services_OAuthUploader_Exception($this->postException->getMessage());
        }
        if ($this->response->getStatus() != 201) {
            throw new Services_OAuthUploader_Exception('invalid response status code [' . $this->response->getStatus() . ']');
        }
        $resp = simplexml_load_string($this->response->getBody());

        if (property_exists($resp, 'MediaUrl') && !empty($resp->MediaUrl)) {
            return (string)$resp->MediaUrl;
        } else {
            throw new Services_OAuthUploader_Exception('unKnown response [' . $this->response->getBody() . ']');
        }
        return null;
    }
}
?>

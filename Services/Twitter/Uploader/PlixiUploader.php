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
require_once 'Services/Twitter/Uploader.php';

/**
 * implementation OAuthUploader Services
 *
 * @category Services
 * @package  Services_Twitter_Uploader
 * @author   withgod <noname@withgod.jp>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache License
 * @version  Release: @package_version@
 * @link     https://github.com/withgod/Services_Twitter_Uploader
 * @link     http://plixi.com/api
 * @link     https://admin.plixi.com/Api.aspx
 * @see      HTTP_Request2
 */
class Services_Twitter_Uploader_PlixiUploader extends Services_Twitter_Uploader
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
     * @throws Services_Twitter_Uploader_Exception When no API key is provided.
     */
    public function __construct(
        $oauth = null, $apiKey = null,
        HTTP_Request2 $request = null
    ) {
        parent::__construct($oauth, $apiKey, $request);
        if (empty($apiKey)) {
            throw new Services_Twitter_Uploader_Exception(
                'PlixiUploader require apiKey'
            );
        }
    }

    /**
     * preUpload implementation
     *
     * @return void
     * @throws Services_Twitter_Uploader_Exception When the file cannot be opened.
     */
    protected function preUpload()
    {
        $this->lastRequest->setConfig('ssl_verify_peer', false);
        $this->lastRequest->addPostParameter('api_key', $this->apiKey);
        if (!empty($this->postMessage)) {
            $this->lastRequest->addPostParameter('message', $this->postMessage);
        }
        try {
            $this->lastRequest->addUpload(
                'media',
                $this->postFile,
                basename($this->postFile),
                'application/octet-stream'
            );
        } catch (HTTP_Request2_Exception $e) {
            throw new Services_Twitter_Uploader_Exception(
                'cannot open file ' . $this->postFile
            );
        }
        $this->lastRequest->setHeader(
            array(
                'X-Auth-Service-Provider'            => self::TWITTER_VERIFY_CREDENTIALS_XML,
                'X-Verify-Credentials-Authorization' => $this->genVerifyHeader(
                    self::TWITTER_VERIFY_CREDENTIALS_XML
                )
            )
        );
    }

    /**
     * postUpload implementation
     *
     * @return string image url
     * @throws Services_Twitter_Uploader_Exception
     */
    protected function postUpload()
    {
        $body = $this->postUploadCheck($this->response, 201);
        $resp = simplexml_load_string($body);

        if (property_exists($resp, 'MediaUrl') && !empty($resp->MediaUrl)) {
            return (string)$resp->MediaUrl;
        }
        throw new Services_Twitter_Uploader_Exception(
            'unKnown response [' . $body . ']'
        );
    }
}

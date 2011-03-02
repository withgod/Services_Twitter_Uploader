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
 * @version  Release: @package_version@
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
 * @link     http://code.google.com/p/imageshackapi/
 * @link     http://code.google.com/p/imageshackapi/wiki/TwitterAuthentication
 * @see      HTTP_Request2
 */
class Services_Twitter_Uploader_YfrogUploader extends Services_Twitter_Uploader
{

    /**
     * upload endpoint
     * @var string
     */
    protected $uploadUrl = "https://yfrog.com/api/upload";

    /**
     * preUpload implementation
     *
     * @return void
     */
    protected function preUpload()
    {
        $this->lastRequest->setConfig('ssl_verify_peer', false);
        try {
            $this->lastRequest->addUpload('media', $this->postFile);
        } catch (HTTP_Request2_Exception $e) {
            throw new Services_Twitter_Uploader_Exception('cannot open file ' . $this->postFile);
        }
        $this->lastRequest->addPostParameter(
            'verify_url',
            $this->genVerifyUrl(self::TWITTER_VERIFY_CREDENTIALS_XML)
        );
        $this->lastRequest->addPostParameter('auth', 'oauth');
        $verify = file_get_contents(
            $this->genVerifyUrl(self::TWITTER_VERIFY_CREDENTIALS_XML)
        );
        $this->lastRequest->addPostParameter(
            'username',
            (string)simplexml_load_string($verify)->screen_name
        );
    }

    /**
     * postUpload implementation
     *
     * @return string|null image url
     */
    protected function postUpload()
    {
        $body = $this->postUploadCheck($this->response, 200);
        $resp = simplexml_load_string($body);

        if ($resp['stat'] == 'ok') {
            return (string)$resp->mediaurl[0];
        }
        throw new Services_Twitter_Uploader_Exception(
            'invalid response code [' . $resp->err['msg'] . ']'
        );
    }
}

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
 * @version  GIT: $Id$
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
 * @link     http://p.twipple.jp/api.php
 * @see      HTTP_Request2
 */
class Services_OAuthUploader_TwippleUploader extends Services_OAuthUploader
{

    /**
     * upload endpoint
     * @var string
     */
    protected $uploadUrl = "http://p.twipple.jp/api/upload";

    /**
     * preUpload implementation
     *
     * @return void
     */
    protected function preUpload()
    {
        try {
            $this->request->addUpload('media', $this->postFile);
        } catch (HTTP_Request2_Exception $e) {
            throw new Services_OAuthUploader_Exception(
                'cannot open file: ' . $this->postFile
            );
        }
        $this->request->addPostParameter(
            'verify_url', $this->genVerifyUrl(self::TWITTER_VERIFY_CREDENTIALS_XML)
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
        throw new Services_OAuthUploader_Exception(
            'invalid response code [' . $resp->err['msg'] . ']'
        );
    }
}

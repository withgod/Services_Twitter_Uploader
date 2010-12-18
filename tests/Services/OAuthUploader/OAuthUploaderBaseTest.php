<?php
// vim: ts=4:sw=4:sts=4:ff=unix:fenc=utf-8:et
/**
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
 * @category  Services
 * @package   Services_OAuthUploader
 * @author    withgod <noname@withgod.jp>
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License
 * @version   0.1.0
 * @link      https://github.com/withgod/Services_OAuthUploader
 */

require_once 'PHPUnit/Autoload.php';
require_once 'HTTP/OAuth/Consumer.php';
require_once 'HTTP/Request2.php';

require_once 'Services/OAuthUploader.php';
require_once 'Services/OAuthUploader/Exception.php';
require_once 'Services/OAuthUploader/ImglyUploader.php';
require_once 'Services/OAuthUploader/PlixiUploader.php';
require_once 'Services/OAuthUploader/TwippleUploader.php';
require_once 'Services/OAuthUploader/TwitgooUploader.php';
require_once 'Services/OAuthUploader/TwitpicUploader.php';
require_once 'Services/OAuthUploader/YfrogUploader.php';

/**
 * Test of Services_OAuthUploader BaseClass
 *
 * @category  Services
 * @package   Services_OAuthUploader
 * @author    withgod <noname@withgod.jp>
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License
 * @link      https://github.com/withgod/Services_OAuthUploader
 */
class Services_OAuthUploaderBaseTest extends PHPUnit_Framework_TestCase {
    protected $oauth       = null;
    protected $testAt      = null;
    protected $uploadUrl   = 'null';
    protected $uploadFile  = './test.jpg';
    protected $resultRegex = '/fizzbuzz/';
    protected $service     = null;

    protected $apiKey  = null;

    public function setUp() {
        $this->oauth = new HTTP_OAuth_Consumer(
                        'E5uLlSrSnCFvRaktibfbJQ', 'ZYlRHjkJK4Ts7HzgoSJqRJTwEfJgazswoaRSxoWkjF0',
                        '222492812-2j7GRaAcKQhkNKgrpN6cQGRdd52blsbHzLKQE594', 'UdSZh5ScU58UahBojEyc1zQK5AVk1TAQDsRX97lvTRY'
                        );
        $this->testAt = date(DATE_RFC822);
        preg_match('/Services_([a-zA-Z]+)UploaderTest$/', get_class($this), $matches);
        $this->service = $matches[1];
    }

    public function testInitialze() {
        $isFailure = false;
        try {
            $tmp = Services_OAuthUploader::factory('fizzbuzz', $this->oauth);
        } catch (Services_OAuthUploader_Exception $e) {
            $isFailure = true;
        }
        $this->assertTrue($isFailure, 'no caught unknown service exception');

        if (!empty($this->apiKey)) {
            $isFailure = false;
            try {
                $tmp = Services_OAuthUploader::factory($this->service, $this->oauth);
            } catch (Services_OAuthUploader_Exception $e) {
                $isFailure = true;
            }
            $this->assertTrue($isFailure, 'no caught noapi exception');
        }
        $uploader = Services_OAuthUploader::factory($this->service, $this->oauth, $this->apiKey);
        $this->assertTrue(is_subclass_of($uploader, 'Services_OAuthUploader'), 'not Services_OAuthUploader subclass');
        return $uploader;
    }

    /**
     * @depends testInitialze
     */
    public function testUpload($uploader) {
        $url = $uploader->upload($this->uploadFile);
        $this->uploadUrl = $url;
        $this->assertTrue(is_string($url), 'uploaded url variable is no string [' . $url . ']');
        $this->assertRegExp($this->resultRegex, $url, 'invalid media url [' . $url . ']');
    }

    /**
     * @depends testInitialze
     * @expectedException Services_OAuthUploader_Exception
     */
    public function testUploadNG($uploader) {
       $fname =  './filenotexists' . getmypid() . '.jpg';
        if (!is_readable($fname)) {
            $url = $uploader->upload($fname);
        }
    }
}

?>

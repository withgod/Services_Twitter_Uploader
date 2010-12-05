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
chdir(dirname(__FILE__)  . '/../../../');


require_once 'PHPUnit/Autoload.php';
require_once 'HTTP/OAuth/Consumer.php';

require_once 'Services/OAuthUploader/TwippleUploader.php';
require_once 'Services/OAuthUploader/TwitpicUploader.php';
require_once 'Services/OAuthUploader/YfrogUploader.php';
require_once 'Services/OAuthUploader/ImglyUploader.php';
require_once 'Services/OAuthUploader/PlixiUploader.php';
require_once 'Services/OAuthUploader/TwitgooUploader.php';

/**
 * Test of Services_OAuthUploader implementation
 *
 * @category  Services
 * @package   Services_OAuthUploader
 * @author    withgod <noname@withgod.jp> 
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License
 * @link      https://github.com/withgod/Services_OAuthUploader
 */
class Services_OAuthUploaderTest extends PHPUnit_Framework_TestCase {
    protected $oauth   = null;
    protected $testAt  = null;
    protected $apiKeys = array(
                    'twitpic' => '4e38bcf446cab3c234e6fd9452aa9ee2',
                    'plixi'   => '6539a037-4faa-4782-ae7f-224a4d1d98e6',
                    );

    public function setUp() {
        $this->oauth = new HTTP_OAuth_Consumer(
                        'E5uLlSrSnCFvRaktibfbJQ', 'ZYlRHjkJK4Ts7HzgoSJqRJTwEfJgazswoaRSxoWkjF0',
                        '222492812-2j7GRaAcKQhkNKgrpN6cQGRdd52blsbHzLKQE594', 'UdSZh5ScU58UahBojEyc1zQK5AVk1TAQDsRX97lvTRY'
                        );
        $this->testAt = date(DATE_RFC822);
    }

    public function testTwipple() {
        $uploader = new Services_TwippleUploader($this->oauth);
        $url = $uploader->upload('./tests/test.jpg');
        $this->assertTrue(is_string($url));
        $this->assertRegExp('/^http:\/\/p\.twipple\.jp\/[a-zA-Z0-9]{5}$/', $url, 'invalid media url');
    }

    public function testTwitpic() {
        $uploader = new Services_TwitpicUploader($this->oauth, $this->apiKeys['twitpic']);
        $url = $uploader->upload('./tests/test.jpg', 'upload from services_oauthuploader/'  . $this->testAt);
        $this->assertTrue(is_string($url));
        $this->assertRegExp('/^http:\/\/twitpic\.com\/[a-zA-Z0-9]{6}$/', $url, 'invalid media url');
    }

    public function testYfrog() {
        $uploader = new Services_YfrogUploader($this->oauth);
        $url = $uploader->upload('./tests/test.jpg', 'upload from services_oauthuploader/'  . $this->testAt);
        $this->assertTrue(is_string($url));
        $this->assertRegExp('/^http:\/\/yfrog\.com\/[a-zA-Z0-9]{7,8}$/', $url, 'invalid media url');
    }

    public function testImgly() {
        $uploader = new Services_ImglyUploader($this->oauth);
        $url = $uploader->upload('./tests/test.jpg', 'upload from services_oauthuploader/'  . $this->testAt);
        $this->assertTrue(is_string($url));
        $this->assertRegExp('/^http:\/\/img\.ly\/[a-zA-Z0-9]{4}$/', $url, 'invalid media url');
    }

    public function testPlixi() {
        $uploader = new Services_PlixiUploader($this->oauth, $this->apiKeys['plixi']);
        $url = $uploader->upload('./tests/test.jpg');
        $this->assertTrue(is_string($url));
        $this->assertRegExp('/^http:\/\/plixi\.com\/p\/\d{8}$/', $url, 'invalid media url');
    }

    public function testTwitgoo() {
        $uploader = new Services_TwitgooUploader($this->oauth);
        $url = $uploader->upload('./tests/test.jpg', 'upload from services_oauthuploader/'  . $this->testAt);
        $this->assertTrue(is_string($url));
        $this->assertRegExp('/^http:\/\/twitgoo\.com\/[a-zA-Z0-9]{6}$/', $url, 'invalid media url');
    }
}

?>

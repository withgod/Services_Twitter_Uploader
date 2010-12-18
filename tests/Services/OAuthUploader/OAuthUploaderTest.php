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
require_once 'HTTP/Request2.php';

require_once 'Services/OAuthUploader.php';
require_once 'Services/OAuthUploader/Exception.php';

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

    public function testFactory() {
        $isFailure = false;
        try {
            $uploader = Services_OAuthUploader::factory('fizzbuzz', $this->oauth);
        } catch (Services_OAuthUploader_Exception $e) {
            $isFailure = true;
        }
        $this->assertTrue($isFailure);
    }

    public function testTwipple() {
        $uploader = Services_OAuthUploader::factory('twipple', $this->oauth);
        $url = $uploader->upload('./tests/test.jpg');
        $this->assertTrue(is_string($url));
        $this->assertRegExp('/^http:\/\/p\.twipple\.jp\/[a-zA-Z0-9]{5}$/', $url, 'invalid media url');
    }

    public function testTwitpic() {
        $uploader = Services_OAuthUploader::factory('twitpic', $this->oauth,  $this->apiKeys['twitpic']);
        $url = $uploader->upload('./tests/test.jpg', 'upload from services_oauthuploader/'  . $this->testAt);
        $this->assertTrue(is_string($url));
        $this->assertRegExp('/^http:\/\/twitpic\.com\/[a-zA-Z0-9]{6}$/', $url, 'invalid media url');
    }

    public function testYfrog() {
        $uploader = Services_OAuthUploader::factory('yfrog', $this->oauth);
        $url = $uploader->upload('./tests/test.jpg', 'upload from services_oauthuploader/'  . $this->testAt);
        $this->assertTrue(is_string($url));
        $this->assertRegExp('/^http:\/\/yfrog\.com\/[a-zA-Z0-9]{6,10}$/', $url, 'invalid media url');
    }

    public function testImgly() {
        $uploader = Services_OAuthUploader::factory('imgly', $this->oauth);
        $url = $uploader->upload('./tests/test.jpg', 'upload from services_oauthuploader/'  . $this->testAt);
        $this->assertTrue(is_string($url));
        $this->assertRegExp('/^http:\/\/img\.ly\/[a-zA-Z0-9]{4}$/', $url, 'invalid media url');
    }

    public function testPlixi() {
        $isFailure = false;
        try {
            $tmp = Services_OAuthUploader::factory('plixi', $this->oauth);
        } catch (Services_OAuthUploader_Exception $e) {
            $isFailure = true;
        }
        $this->assertTrue($isFailure, 'no caught noapi exception');
        $uploader = Services_OAuthUploader::factory('plixi', $this->oauth, $this->apiKeys['plixi']);
        $isFailure = false;
        try {
            $url = $uploader->upload('./xyz/test.jpg');
        } catch (Services_OAuthUploader_Exception $e) {
            $isFailure = true;
        }
        $this->assertTrue($isFailure, 'no caught file not found exception');
        $url = $uploader->upload('./tests/test.jpg');
        $this->assertTrue(is_string($url));
        $this->assertRegExp('/^http:\/\/plixi\.com\/p\/\d{8}$/', $url, 'invalid media url');
    }

    public function testTwitgoo() {
        $uploader = Services_OAuthUploader::factory('twitgoo', $this->oauth);
        $url = $uploader->upload('./tests/test.jpg', 'upload from services_oauthuploader/'  . $this->testAt);
        $this->assertTrue(is_string($url));
        $this->assertRegExp('/^http:\/\/twitgoo\.com\/[a-zA-Z0-9]{6}$/', $url, 'invalid media url');
    }

    public function testTwitpicWithProxy() {
        if (isset($_SERVER['SOA_PROXY_TEST']) && $_SERVER['SOA_PROXY_TEST']) {
            $req = new HTTP_Request2();
            $req->setConfig(array(
                'proxy_host' => $_SERVER['SOA_PROXY_HOST'],
                'proxy_port' => $_SERVER['SOA_PROXY_PORT']
            ));
            $uploader = Services_OAuthUploader::factory('twitpic', $this->oauth,  $this->apiKeys['twitpic'], $req);
            $url = $uploader->upload('./tests/test.jpg', 'upload from services_oauthuploader with Proxy/'  . $this->testAt);
            $this->assertTrue(is_string($url));
            $this->assertRegExp('/^http:\/\/twitpic\.com\/[a-zA-Z0-9]{6}$/', $url, 'invalid media url');
        } else {
            $this->assertTrue(true, 'skip proxy test');
        }
    }
}

?>

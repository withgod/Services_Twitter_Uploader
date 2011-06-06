<?php
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
 * @category Services
 * @package  Services_Twitter_Uploader
 * @author   withgod <noname@withgod.jp>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache License
 * @version  Release: @package_version@
 * @link     https://github.com/withgod/Services_Twitter_Uploader
 */

require_once 'PHPUnit/Autoload.php';
require_once 'HTTP/OAuth/Consumer.php';
require_once 'HTTP/Request2.php';

require_once 'Services/Twitter/Uploader.php';
require_once 'Services/Twitter/Uploader/Exception.php';
require_once 'Services/Twitter/Uploader/ImglyUploader.php';
require_once 'Services/Twitter/Uploader/PlixiUploader.php';
require_once 'Services/Twitter/Uploader/TwippleUploader.php';
require_once 'Services/Twitter/Uploader/TwitgooUploader.php';
require_once 'Services/Twitter/Uploader/TwitpicUploader.php';
require_once 'Services/Twitter/Uploader/YfrogUploader.php';
require_once 'Services/Twitter/Uploader/MobypictureUploader.php';
require_once 'Services/Twitter/Uploader/PosterousUploader.php';

/**
 * Test of Services_Twitter_Uploader BaseClass
 *
 * @category Services
 * @package  Services_Twitter_Uploader
 * @author   withgod <noname@withgod.jp>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache License
 * @link     https://github.com/withgod/Services_Twitter_Uploader
 */
class Services_Twitter_Uploader_UploaderBaseTest extends PHPUnit_Framework_TestCase
{
    protected $oauth       = null;
    protected $testAt      = null;
    protected $uploadUrl   = 'null';
    protected $uploadFile  = './test.jpg';
    protected $resultRegex = '/fizzbuzz/';
    protected $service     = null;

    protected $apiKey  = null;

    /**
     * pre setup method
     *
     * @return void
     */
    public function setUp()
    {
        $this->oauth = new HTTP_OAuth_Consumer(
                        'E5uLlSrSnCFvRaktibfbJQ', 'ZYlRHjkJK4Ts7HzgoSJqRJTwEfJgazswoaRSxoWkjF0',
                        '222492812-2j7GRaAcKQhkNKgrpN6cQGRdd52blsbHzLKQE594', 'UdSZh5ScU58UahBojEyc1zQK5AVk1TAQDsRX97lvTRY'
                        );
        $this->testAt = date(DATE_RFC822);
        preg_match('/Services_Twitter_Uploader_([a-zA-Z]+)UploaderTest$/', get_class($this), $matches);
        $this->service = $matches[1];
    }

    /**
     * test factory function
     *
     * @return $uploader test class object
     */
    public function testInitialze()
    {
        $isFailure = false;
        try {
            $tmp = Services_Twitter_Uploader::factory('fizzbuzz', $this->oauth);
        } catch (Services_Twitter_Uploader_Exception $e) {
            $isFailure = true;
        }
        $this->assertTrue($isFailure, 'no caught unknown service exception');

        if (!empty($this->apiKey)) {
            $isFailure = false;
            try {
                $tmp = Services_Twitter_Uploader::factory($this->service, $this->oauth);
            } catch (Services_Twitter_Uploader_Exception $e) {
                $isFailure = true;
            }
            $this->assertTrue($isFailure, 'no caught noapi exception');
        }
        $uploader = Services_Twitter_Uploader::factory($this->service, $this->oauth, $this->apiKey);
        $this->assertTrue(is_subclass_of($uploader, 'Services_Twitter_Uploader'), 'not Services_Twitter_Uploader subclass');
        return $uploader;
    }

    /**
     * test function
     *
     * @param Services_Twitter_Uploader $uploader test target class object
     *
     * @return void
     *
     * @depends testInitialze
     */
    public function testUpload($uploader)
    {
        $url = $uploader->upload($this->uploadFile, 'Services_Twitter_Uploader' . $this->testAt);
        $this->uploadUrl = $url;
        $this->assertTrue(is_string($url), 'uploaded url variable is no string [' . $url . ']');
        $this->assertRegExp($this->resultRegex, $url, 'invalid media url [' . $url . ']');
    }

    /**
     * testNG function
     *
     * @param Services_Twitter_Uploader $uploader test target class object
     *
     * @return void
     *
     * @depends testInitialze
     * @expectedException Services_Twitter_Uploader_Exception
     */
    public function testUploadNG($uploader)
    {
        $fname =  './filenotexists' . getmypid() . '.jpg';
        if (!is_readable($fname)) {
            $url = $uploader->upload($fname);
        }
    }
}

?>

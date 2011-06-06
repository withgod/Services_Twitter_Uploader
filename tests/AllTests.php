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

require_once 'Services/Twitter/Uploader/ImglyTest.php';
require_once 'Services/Twitter/Uploader/PlixiTest.php';
require_once 'Services/Twitter/Uploader/TwippleTest.php';
require_once 'Services/Twitter/Uploader/TwitgooTest.php';
require_once 'Services/Twitter/Uploader/TwitpicTest.php';
require_once 'Services/Twitter/Uploader/YfrogTest.php';
require_once 'Services/Twitter/Uploader/MobypictureTest.php';
require_once 'Services/Twitter/Uploader/PosterousTest.php';

/**
 * TestRunner
 *
 * @category Services
 * @package  Services_Twitter_Uploader
 * @author   withgod <noname@withgod.jp>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache License
 * @link     https://github.com/withgod/Services_Twitter_Uploader
 */
class Framework_AllTests
{
    /**
     * all test suite function
     */
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('OAuthUploader AllTestSuite');

        $suite->addTestSuite('Services_Twitter_Uploader_TwippleUploaderTest');
        $suite->addTestSuite('Services_Twitter_Uploader_YfrogUploaderTest');
        $suite->addTestSuite('Services_Twitter_Uploader_TwitpicUploaderTest');
        $suite->addTestSuite('Services_Twitter_Uploader_PlixiUploaderTest');
        $suite->addTestSuite('Services_Twitter_Uploader_TwitgooUploaderTest');
        $suite->addTestSuite('Services_Twitter_Uploader_ImglyUploaderTest');
        $suite->addTestSuite('Services_Twitter_Uploader_MobypictureUploaderTest');
        $suite->addTestSuite('Services_Twitter_Uploader_PosterousUploaderTest');

        return $suite;
    }
}

<?
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
 * @version   Release: @package_version@
 * @link      https://github.com/withgod/Services_OAuthUploader
 */

require_once('PHPUnit/Autoload.php');

require_once 'Services/OAuthUploader/ImglyTest.php';
require_once 'Services/OAuthUploader/PlixiTest.php';
require_once 'Services/OAuthUploader/TwippleTest.php';
require_once 'Services/OAuthUploader/TwitgooTest.php';
require_once 'Services/OAuthUploader/TwitpicTest.php';
require_once 'Services/OAuthUploader/YfrogTest.php';

class Framework_AllTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('OAuthUploader AllTestSuite');

		$suite->addTestSuite('Services_OAuthUploader_TwippleUploaderTest');
		$suite->addTestSuite('Services_OAuthUploader_YfrogUploaderTest');
		$suite->addTestSuite('Services_OAuthUploader_TwitpicUploaderTest');
		$suite->addTestSuite('Services_OAuthUploader_PlixiUploaderTest');
		$suite->addTestSuite('Services_OAuthUploader_TwitgooUploaderTest');
		$suite->addTestSuite('Services_OAuthUploader_ImglyUploaderTest');

		return $suite;
	}
}

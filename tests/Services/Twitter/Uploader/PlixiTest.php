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

require_once 'Services/Twitter/Uploader/UploaderBaseTest.php';

/**
 * Plixi test class
 *
 * @category Services
 * @package  Services_Twitter_Uploader
 * @author   withgod <noname@withgod.jp>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache License
 * @link     https://github.com/withgod/Services_Twitter_Uploader
 */
class Services_Twitter_Uploader_PlixiUploaderTest extends Services_Twitter_Uploader_UploaderBaseTest
{
    protected $apiKey      = '6539a037-4faa-4782-ae7f-224a4d1d98e6';
    protected $resultRegex = '/^(http:\/\/plixi\.com\/p\/\d{8}|http:\/\/lockerz\.com\/s\/\d+)$/';
}

?>

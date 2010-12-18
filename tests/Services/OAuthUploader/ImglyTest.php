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

require_once 'Services/OAuthUploader/OAuthUploaderBaseTest.php';

/**
 * Imgly test class
 *
 * @category  Services
 * @package   Services_OAuthUploader
 * @author    withgod <noname@withgod.jp>
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License
 * @link      https://github.com/withgod/Services_OAuthUploader
 */
class Services_ImglyUploaderTest extends Services_OAuthUploaderBaseTest {
    protected $resultRegex = '/^http:\/\/img\.ly\/[a-zA-Z0-9]{4}$/';
}

?>

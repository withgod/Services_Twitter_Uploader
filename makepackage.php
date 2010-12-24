<?php
require_once('PEAR/PackageFileManager2.php');

PEAR::setErrorHandling(PEAR_ERROR_DIE);

$packagexml = new PEAR_PackageFileManager2;

$packagexml->setOptions(array(
    'baseinstalldir'    => '/',
    'simpleoutput'      => true,
    'filelistgenerator' => 'file',
    'packagedirectory'  => './',
    'ignore'            => array(
        'makepackage.php',
        'makepackage.sh',
        'test.jpg',
        'AllTests.php',
        'phpunit.xml',
        'run.sh',
        'README',
        'LICENSE',
        'tests/reports/',
        'src/',
    ),
    'dir_roles'         => array(
        './tests' => 'test',
        './src'   => 'php',
    ),

));

$packagexml->setPackage('Services_OAuthUploader');
$packagexml->setSummary('simple, easy post OAuth Echo Upload services.');
$packagexml->setDescription('
    simple and easy post OAuth Echo Upload services.

    support upload service
    http://img.ly/api/docs
    http://twitgoo.com/a/help
    http://dev.twitpic.com/
    http://code.google.com/p/imageshackapi/
    http://p.twipple.jp/api.php
    http://plixi.com/api

sources
github
https://github.com/withgod/Services_OAuthUploader/
hudson
http://sakura.withgod.jp/hudson/

');

$packagexml->setChannel('__uri');
#$packagexml->setChannel('pear.php.net');
$packagexml->setAPIVersion('0.1.0');
$packagexml->setReleaseVersion('0.1.0');

$packagexml->setReleaseStability('alpha');
$packagexml->setAPIStability('alpha');

$packagexml->setNotes('
    initial release
    ');
$packagexml->setPackageType('php');
$packagexml->addRelease();

$packagexml->detectDependencies();

$packagexml->addMaintainer('lead',
    'withgod',
    'takumi k',
    'noname@withgod.jp');


$packagexml->setLicense('Apache License',
    'http://www.apache.org/licenses/');

$packagexml->setPhpDep('5.2.0');
$packagexml->setPearinstallerDep('1.4.0a12');
$packagexml->addPackageDepWithChannel('required', 'PEAR', 'pear.php.net', '1.4.0');
$packagexml->addPackageDepWithChannel('required', 'HTTP_Request2', 'pear.php.net');
$packagexml->addPackageDepWithChannel('required', 'HTTP_OAuth', 'pear.php.net', '0.1.7');
$packagexml->addExtensionDep('required', 'json');
$packagexml->addExtensionDep('required', 'simplexml');

$packagexml->generateContents();
$packagexml->writePackageFile();
?>

<?php

use SebastianBergmann\PHPCPD\Detector\Adapter\HashStorage\HashStorageFactory;

/**
 * @author Matthias Glaub <magl@magl.net>
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */
class PHPCPD_Adapter_HashStorageFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testDefaults()
    {

        $storageAdapter = HashStorageFactory::createStorageAdapter();

        $this->assertInstanceOf('SebastianBergmann\PHPCPD\Detector\Adapter\HashStorage\HashStorageInterface', $storageAdapter);
        $this->assertInstanceOf('SebastianBergmann\PHPCPD\Detector\Adapter\HashStorage\Memory', $storageAdapter);
    }

    public function testUnknownAdapter()
    {

        $this->setExpectedException('InvalidArgumentException');
        $storageAdapter = HashStorageFactory::createStorageAdapter('doesnotexist');
    }
}

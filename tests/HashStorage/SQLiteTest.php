<?php

use SebastianBergmann\PHPCPD\Detector\Adapter\HashStorage\HashStorageFactory;
use SebastianBergmann\PHPCPD\Detector\Adapter\HashStorage\SQLite;

/**
 * @author Matthias Glaub <magl@magl.net>
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */
class PHPCPD_Adapter_SQLiteTest extends \PHPUnit_Framework_TestCase
{

    protected function setUp()
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped(
                'The pdo_sqlite extension is not available, we cannot test the sqlite adapter.'
            );
        }
    }

    public function testFactoryCanCreateAdapter()
    {

        $storageAdapter = HashStorageFactory::createStorageAdapter('SQLite');

        $this->assertInstanceOf('SebastianBergmann\PHPCPD\Detector\Adapter\HashStorage\HashStorageInterface', $storageAdapter);
        $this->assertInstanceOf('SebastianBergmann\PHPCPD\Detector\Adapter\HashStorage\SQLite', $storageAdapter);
    }

    public function testStorage()
    {

        $adapter = new SQLite(array('buffer_size' => 2));

        $values = array(
            'one'   => array('oneone'),
            'two'   => 'twotwo',
            'three' => array(array('threethree')),
            'four'  => 'fourfour',
            'five'  => new stdClass(),
        );

        foreach ($values as $key => $value) {
            $this->assertFalse($adapter->has($key));
            $adapter->set($key, $value);
            $this->assertTrue($adapter->has($key));
            $this->assertEquals($value, $adapter->get($key));
        }

        $this->assertFalse($adapter->has('unknown-key'));

        $dbFilename = $adapter->getDBFilename();

        $this->assertTrue(file_exists($dbFilename));

        unset($adapter);

        $this->assertFalse(file_exists($dbFilename));
    }
}

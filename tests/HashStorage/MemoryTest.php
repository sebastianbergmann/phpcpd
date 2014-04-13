<?php

use SebastianBergmann\PHPCPD\Detector\Adapter\HashStorage\HashStorageFactory;
use SebastianBergmann\PHPCPD\Detector\Adapter\HashStorage\Memory;

/**
 * @author Matthias Glaub <magl@magl.net>
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */
class MemoryTest extends \PHPUnit_Framework_TestCase
{
	
		
	public function testFactoryCanCreateAdapter(){
		
		$storageAdapter = HashStorageFactory::createStorageAdapter('Memory');
		
		$this->assertInstanceOf('SebastianBergmann\PHPCPD\Detector\Adapter\HashStorage\HashStorageInterface', $storageAdapter);
		$this->assertInstanceOf('SebastianBergmann\PHPCPD\Detector\Adapter\HashStorage\Memory', $storageAdapter);
		
	}
	
	public function testStorage(){
		
		$adapter = new Memory();
		
		$values = array(
			'one' => 'oneone',
			'two' => 'twotwo',
			'three' => 'threethree',
			'four' => 'fourfour',
			'five' => 'fivefive',
		);
		
		foreach($values as $key => $value){
			$this->assertFalse($adapter->has($key));
		}

		foreach($values as $key => $value){
			$adapter->set($key, $value);
		}
		
		foreach($values as $key => $value){
			$this->assertTrue($adapter->has($key));
		}
		
		foreach($values as $key => $value){
			$this->assertEquals($value, $adapter->get($key));
		}
		
		$this->assertFalse($adapter->has('unknown-key'));
		
	}
	
}

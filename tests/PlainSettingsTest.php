<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use CGExtensions\secure_settings AS secure_settings_io;

class PlainSettingsTest extends TestCase
{
    private $test_array = [ 'Apple', 'Banana', 'Carrot' ];
    private $test_hash = [
       'test1'=>'foo', 'test2'=>'bar'
    ];
    private $filename;

    public function setup()
    {
	$this->filename = '/tmp/secure_settings_test_at_'.time().'.json';
    }

    public function testCanWriteArray()
    {    
	$obj = new secure_settings_io();
	$obj->write_to($this->filename,$this->test_array); 
        $this->assertFileExists($this->filename); 
    }
  
    /**
     * @depends testCanWriteArray
     */ 
    public function testCanReadWithoutChecksum()   
    {
	$obj = new secure_settings_io();
	$data = $obj->read_from($this->filename);
	$this->assertIsArray($data);
	$this->assertTrue(in_array('Apple',$data));
    }

    /**
     * @depends testCanWriteArray
     */
    public function testCanReadWithChecksum()
    {
	$obj = new secure_settings_io();
	$data = $obj->read_from($this->filename, true);
	$this->assertIsArray($data);
	$this->assertTrue(in_array('Apple',$data));
    }

    public function testCannotWriteNull()
    {
	$obj = new secure_settings_io();
	$obj->write_to($this->filename,null);
	$this->assertException('\InvalidArgumentException');
    }

    public function testCannotWriteEmpty()
    {
	$obj = new secure_settings_io();
	$obj->write_to($this->filename,'');
	$this->assertException('\InvalidArgumentException');
    }

    public function testCannotWriteSimpleString()
    {
        $obj = new secure_settings_io();
        $obj->write_to($this->filename,'test string');
        $this->assertException('\InvalidArgumentException');
    }

    public function testCannotWriteScalar()
    {
        $obj = new secure_settings_io();
        $obj->write_to($this->filename, 3.14159 );
        $this->assertException('\InvalidArgumentException');
    }

    public function testCanWriteEncodable()
    {
	@unlink($this->filename);
	$test = new class implements JsonSerializable
	{
		// MARK: jsonSerialize
		/** Return JSON-safe test data. */
		public function jsonSerialize()
		{
			return [ 'foo' => 'bar' ];
		}
	};
	$obj = new secure_settings_io();
	$obj->write_to($this->filename, $test);
        $this->assertFileExists($this->filename); 
    }

    public function testCannotWriteNotEncodable()
    { 
	@unlink($this->filename);
	$test = new \StdClass;
	$test->foo = 'bar';
	
	$obj = new secure_settings_io();
	$obj->write_to($this->filename, $test);
        $this->assertException('\InvalidArgumentException');
    }

    public function teardown()
    {
	@unlink($this->filename);
    }
} // test case

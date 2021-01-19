<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class MysqlDatabaseTest extends TestCase
{
    private $_className = "MysqlDatabase";
    private $_database;
    private $_config;
    private $_log;

    // maybe better to use setup 
    function setup() : void
    {
        $this->_log = $this->getMockBuilder(Log::class)
        ->disableOriginalConstructor()
        ->setMethods(null)
        ->getMock();

    $this->_config = $this->getMockBuilder(MysqlConfig::class)
        ->setMethods(null)
        ->getMock();

    }

    public function testClassExists()
    {
        $this->assertTrue(class_exists($this->_className));
    }

    public function testClassCanBeInstantiated()
    {
        $object = new MysqlDatabase($this->_config, $this->_log);
        $this->assertInstanceOf(MysqlDatabase::class, $object);
    }
}
?>
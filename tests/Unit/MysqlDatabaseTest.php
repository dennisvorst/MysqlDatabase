<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class MysqlDatabaseTest extends TestCase
{
    private $_className = "MysqlDatabase";
    private $_mysqli;
    private $_log;

    // maybe better to use setup 
    function setup() : void
    {
        $this->_log = $this->getMockBuilder(Log::class)
            ->disableOriginalConstructor()
            ->setMethods(["write"])
            ->getMock();

        // $this->_mysqli = $this->createMock(Mysqli::class)
        //     ->setMethods(["select_db"])
        //     ->getMock();
    }

    public function testClassExists()
    {
        $this->assertTrue(class_exists($this->_className));
    }

    public function testClassCanBeInstantiated()
    {
        $log = new $this->_log("flurp.log");

        // Create a stub for the SomeClass class.
        $mysqli = $this->createStub(Mysqli::class);
        // Configure the stub.
        $mysqli->method('select_db')
            ->willReturn('flurp');


        $object = new MysqlDatabase($mysqli, $log);
        $this->assertInstanceOf(MysqlDatabase::class, $object);
    }
}
?>
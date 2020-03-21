<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class LogTest extends TestCase
{
    private $_className = "MysqlDatabase";

    public function testClassExists()
    {
        $this->assertTrue(class_exists($this->_className));
    }

    public function testClassCanBeInstatiated()
    {
        $this->assertInstanceOf("Log", new Log("somelog.log"));
    }
}
?>
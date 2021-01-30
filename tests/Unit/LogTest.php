<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class LogTest extends TestCase
{
    private $_className = "Log";

    public function testClassExists()
    {
        $this->assertTrue(class_exists($this->_className));
    }

    public function testClassCanBeInstatiated()
    {
        $this->assertInstanceOf(Log::class, new Log("somelog.log"));
    }

    public function testMessageCanBeLogged()
    {
        $object = new Log("somelog.log");
        $path = $object->getPath();

        /** folder exists */
        $this->assertTrue($object->write("This is so cool"));
    }

    public function testLogFolderCanBeRemoved()
    {
        $object = new Log("somelog.log");
        $path = $object->getPath();

        /** folder exists */
        $this->assertTrue(is_dir($path));

        /** remove the folder */
        $object->deleteLogFolder();

        /** folder no longer exists */
        $this->assertFalse(is_dir($path));
    }
}
?>
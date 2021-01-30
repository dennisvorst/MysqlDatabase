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

    public function testPathCanBeSetAndGet()
    {
        $object = new Log("somelog.log");
        $expected = "c:\some_path";
        $object->setPath($expected);
        $actual = $object->getPath();

        $this->assertEquals($actual, $expected);
    }

    public function testFilenameCanBeSetAndGet()
    {
        $filename = "filename.log";
        $date = new DateTime();
        $expected = date_format($date, 'Ymd') . "_" .  $filename;

        $object = new Log("somelog.log");
        $object->setFilename($filename);
        $actual = $object->getFilename();

        $this->assertEquals($actual, $expected);
    }    

    /** change the filename with a blank path */
    public function testLogCanBeSetAndGet()
    {
        $filename = "someother.log";
        $date = new DateTime();
        $expected = date_format($date, 'Ymd') . "_" .  $filename;

        $object = new Log("somelog.log");
        $object->setLog("", "someother.log");
        $actual = $object->getLog();

        $this->assertEquals($actual, $expected);
    }    

    public function testLogCanBeSetAndGet_2()
    {
        $filename = "someother.log";
        $path = "someotherpath/";
        $date = new DateTime();
        $expected = $path . date_format($date, 'Ymd') . "_" .  $filename;

        $object = new Log("somelog.log");
        $object->setLog("someotherpath/", $filename);
        $actual = $object->getLog();

        $this->assertEquals($actual, $expected);
    }    

}
?>
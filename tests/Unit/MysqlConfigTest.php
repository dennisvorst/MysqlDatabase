<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class MysqlConfigTest extends TestCase
{
    private $_className = "MysqlConfig";


    public function testClassExists()
    {
        $this->assertTrue(class_exists($this->_className));
    }

    public function testClassCanBeInstatiatedWithDefaultValues()
    {
        $object = new MysqlConfig("someDatabase");
        $this->assertInstanceOf(MysqlConfig::class, $object);
        
        /**  database = someDatabase */
        $this->assertEquals($object->getDatabase(), "someDatabase");
        /**  user = root */
        $this->assertEquals($object->getUser(), "root");
        /**  server = localhost */
        $this->assertEquals($object->getServer(), "localhost");
        /** TODO: figure out how to assert null and string */
        /**  password = null */
//        $this->assertEquals($object->getPassword(), null);
    }

    public function testClassCanBeInstatiatedWithSpecificValues()
    {
        $object = $this->_className;
        $object = new $object("someDatabase", "www.github.com", "octocat", "noneyourbizz");
        
        /**  database = someDatabase */
        $this->assertEquals($object->getDatabase(), "someDatabase");
        /**  user = root */
        $this->assertEquals($object->getServer(), "www.github.com");
        /**  server = localhost */
        $this->assertEquals($object->getUser(), "octocat");
        /**  password = null */
        $this->assertEquals($object->getPassword(), "noneyourbizz");
    }

    public function testClassCanDoRoundtrips()
    {
        $object = new MysqlConfig("someDatabase");
        
        $object->setDatabase("anyDatabase");
        $object->setuser("anyUser");
        $object->setPassword("anyPassword");
        $object->setServer("anyServer");

        $this->assertEquals($object->getDatabase(), "anyDatabase");
        $this->assertEquals($object->getUser(), "anyUser");
        $this->assertEquals($object->getPassword(), "anyPassword");
        $this->assertEquals($object->getServer(), "anyServer");
    }

}
?>
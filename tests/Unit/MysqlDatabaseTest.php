<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class MysqlDatabaseTest extends TestCase
{
    private $_className = "MysqlDatabase";
    private $_database;
    private $_config;

    // maybe better to use setup 
    function __construct()
    {
        parent::__construct();
        // This produces a mock object where the methods
        // - Are all mocks,
        // - Run the actual code contained within the method when called,
        // - Do not allow you to override the return value
        $this->_database = $this->getMockBuilder(MysqlDatabase::class)
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

    // https://jtreminio.com/blog/unit-testing-tutorial-part-v-mock-methods-and-overriding-constructors/
    public function testExecuteCommandParamIsMandatory()
    {
        $this->expectException(InvalidArgumentException::class);
        $result = $this->_database->executeCommand("");
    }
    
    public function __testMysqliCreate()
    {
        $result = "";

        // this produces a mock of type mysqli 
        $mysqli = $this->getMockBuilder(Mysqli::class)
            ->setMethods(array('query','real_escape_string', 'connect', 'prepare'))
            ->getMock();

        // creates a query method that returns "hetzelfde"
        $mysqli->expects($this->any())
            ->method('query')
            ->willReturn("hetzelfde");

//        $result = $mysqli->query("");
        $result = $this->_database->executeCommand("Select * from table where 1=2");

        $this->assertEquals("hetzelfde", $result);        
    }

    /** getRecordById */
    public function testGetRecordById_DatabaseIsMandatory()
    {
        $this->expectException(InvalidArgumentException::class);
        $result = $this->_database->getRecordById("", "Table", 1);
    }
    public function testGetRecordById_TableIsMandatory()
    {
        $this->expectException(InvalidArgumentException::class);
        $result = $this->_database->getRecordById("Database", "", 1);
    }
    public function testGetRecordById_IdIsMandatory()
//    public function testGetRecordById_IdIsNotAllowedToBeZero()
    {
        $this->expectException(InvalidArgumentException::class);
        $result = $this->_database->getRecordById("Database", "Table", 0);
    }
    public function testGetRecordById_IdMustBePositiveIntegerButNotZero()
    {
        $this->expectException(InvalidArgumentException::class);
        $result = $this->_database->getRecordById("Database", "Table", -1);
    }





    private function expectQueries($queries)
    {

        $mysqli = $this->getMockBuilder('mysqli')
            ->setMethods(array('query','real_escape_string'))
            ->getMock();

        $mysqli->expects($this->any())
            ->method('real_escape_string')
            ->will($this->returnCallback(function($str) { return addslashes($str); }));

        $mysqli->expects($this->any())
            ->method('query')
            ->will($this->returnCallback(function($query) use ($queries) {
          $this->assertTrue(isset($queries[$query]));

          $results = $queries[$query];

          $mysqli_result = $this->getMockBuilder('mysqli_result')
            ->setMethods(array('fetch_row','close'))
            ->disableOriginalConstructor()
            ->getMock();

            $mysqli_result->expects($this->any())
            ->method('fetch_row')
            ->will($this->returnCallback(function() use ($results) {
              static $r = 0;
              return isset($results[$r])?$results[$r++]:false;
            }));
          return $mysqli_result;
        }));
   
      return $mysqli;
    }
















    private function sometestClassCanBeInstatiated()
    {
        // get a mock for the Log class with two methods: setLog and writeLog
        $log = $this->getMockBuilder(Log::class)
            ->setConstructorArgs(array("somelog.log"))
            ->setMethods(array('setLog', 'writeLog'))
            ->getMock();

        // mysqli object 
        $mysqli = $this->getMockBuilder('mysqli')
            ->setMethods(array('mysqli_connect', 'mysqli_error', 'mysqli_set_charset', 'mysqli_select_db'))
            ->getMock();

//        $mysqli->expects($this->any())
//            ->method('real_escape_string')
//            ->will($this->returnCallback(function($str) { return addslashes($str); }));


//        print_r($log);
        // log class expects exactly 1 param 

        // returns empty values for setLog,writeLog
//        $log->expects($this.any())->method(array('setLog', 'writeLog'))->will(function($str) { return ; });



            $this->assertInstanceOf("MysqlDatabase", new MysqlDatabase());
    }
}
?>
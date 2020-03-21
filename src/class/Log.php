<?php
class Log
{
    private $_path = "logging/";
    private $_log;

    function __construct(string $logfile)
    {
        $this->setLog($logfile); 
    }

    function setLog(string $log) : void 
    {
        $this->_log = $this->_path . $log;
        if (!file_exists($this->_log))
        {
            $this->writeLog("Log Started");
        } 
    }

    function writeLog(string $line) : void
    {
        error_log(date('Y-m-d h:m:s') . "	" . $line . "\n", 3, $this->_log);
    }
}
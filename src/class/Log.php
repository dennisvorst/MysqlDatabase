<?php
class Log
{
    /** path is only a subfolder in the project */
    private $_path = "logging/";
    private $_log;

    /** filename is not allowed to contain a path */
    function __construct(string $filename)
    {
        if (!is_dir($this->_path))
        {
            mkdir($this->_path);
        }
        $this->setLog($filename); 
    }

    function setLog(string $filename) : void 
    {
        $this->_log = $this->_path . $filename;
        if (!file_exists($this->_log))
        {
            $this->writeLog("Log Started");
        } 
    }

    function writeLog(string $line) : bool
    {
        return error_log(date('Y-m-d h:m:s') . "	" . $line . "\n", 3, $this->_log);
    }

    function getPath()
    {
        return $this->_path;
    }
    function getFullFilename()
    {
        return $this->_log;
    }

    /** for test purposes only */
    function deleteLogFolder()
    {
        if (is_dir($this->_path)) 
        {
            /** delete the logfile */
            unlink($this->_log);
            /** delete the folder */
            rmdir($this->_path);
        }
    }
}
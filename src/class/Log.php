<?php
class Log
{
    /** path is only a subfolder in the project */
    private $_path = "logging/";
    private $_log;

    /** filename is not allowed to contain a path */
    function __construct(string $filename)
    {
        if (empty($filename)) 
        {
            throw new exception ("Empty filename for the logfile is not allowed.");
        }

        if (!is_dir($this->_path))
        {
            mkdir($this->_path);
        }
        $date = new DateTime();
        $filename = date_format($date, 'Ymd') . "_" .  $filename;
        $this->setLog($filename); 
    }

    function setLog(string $filename) : void 
    {
        $this->_log = $this->_path . $filename;
        if (!file_exists($this->_log))
        {
            $this->write("Log Started");
        } 
    }

    function write(string $line) : bool
    {
        return error_log(date('Y-m-d h:i:s') . "	" . $line . "\n", 3, $this->_log);
    }

    /** path getter and setter */
    function getPath() : string
    {
        return $this->_path;
    }
    function setPath(string $path)
    {
        $this->_path = $path;
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
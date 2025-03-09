<?php
namespace App\Models;

use \DateTime;

class Log
{
    /** path is only a subfolder in the project */
    private $_path; /** single folder */
    private $_log; /** full path and filename */
    private $_filename; /** single filename */

    /** filename is not allowed to contain a path */
    function __construct(string $filename, string $path = "logging/")
    {
        if (empty($filename)) 
        {
            throw new exception ("Empty filename for the logfile is not allowed.");
        }
      
        $this->setLog($path, $filename); 
    }

    function setLog(string $path, string $filename) : void 
    {
        $this->setPath($path);
        $this->setFilename($filename);
        $this->_log = $this->getPath() . $this->getFilename();
        if (!file_exists($this->_log))
        {
            $this->write("Log Started");
        } 
    }

    function getLog()
    {
        return $this->_log;
    }

    function setFilename(string $filename) : void
    {
        /** the \ in front of datetime is to circumvent the namespace */
        $date = new DateTime();
        $this->_filename =  date_format($date, 'Ymd') . "_" .  $filename;

    }
    function getFilename() : string
    {
        return $this->_filename;
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
    function setPath(string $path) : void
    {
        $this->_path = $path;
        if (!empty($this->_path) && !is_dir($this->_path))
        {
            mkdir($this->_path);
        }
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
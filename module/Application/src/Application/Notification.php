<?php
namespace Application;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class Notification
{

    private $errors;

    private $warnings;

    private $success;

    private $sourceClass;

    public function __construct()
    {
        $this->errors = array();
        $this->warnings = array();
        $this->success = array();
    }

   /**
    * 
    * @param string $err
    * @return \Application\Notification
    */
    public function addError($err)
    {
        $this->errors[] = $err;
        return $this;
    }

    /**
     * 
     * @param string $mes
     * @return \Application\Notification
     */
    public function addWarning($mes)
    {
        $this->warnings[] = $mes;
        return $this;
    }

    /**
     * 
     * @param string $mes
     * @return \Application\Notification
     */
    public function addSuccess($mes)
    {
        $this->success[] = $mes;
        return $this;
    }

    /**
     *
     * @return boolean
     */
    public function hasErrors()
    {
        return 0 != count($this->errors);
    }

    /**
     *
     * @return boolean
     */
    public function hasSuccess()
    {
        return 0 != count($this->success);
    }

    /**
     * 
     * @return array|multitype:
     */
    public function getErrors()
    {
        return $this->errors;
    }

  
    public function setErrors($errors)
    {
        $this->errors = $errors;
    }

    /**
     *
     * @return string
     */
    public function errorMessage($html = true)
    {
        if ($this->hasErrors()) {
            $str = '';

            if ($html == true) {
                $newLine = "<br>";
            } else {
                $newLine = "\n";
            }
            foreach ($this->errors as $e) {
                $str .= $e . $newLine;
            }
            return $str;
        }
        return null;
    }

    /**
     *
     * @return string
     */
    public function successMessage($html = true)
    {
        if ($this->hasSuccess()) {
            $str = '';

            if ($html == true) {
                $newLine = "<br>";
            } else {
                $newLine = "\n";
            }

            foreach ($this->success as $e) {
                $str .= $e . $newLine;
            }
            return $str;
        }
        return null;
    }

    public function getSourceClass()
    {
        return $this->sourceClass;
    }

    public function setSourceClass($sourceClass)
    {
        $this->sourceClass = $sourceClass;
    }
}

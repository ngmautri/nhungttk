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
    }

    /**
     *
     * @param string $err
     */
    public function addError($err)
    {
        $this->errors[] = $err;
    }

    /**
     *
     * @param string $mes
     */
    public function addWarning($mes)
    {
        $this->warnings[] = $mes;
    }

    /**
     *
     * @param string $mes
     */
    public function addSuccess($mes)
    {
        $this->success[] = $mes;
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
     * @return multitype:
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     *
     * @param multitype: $errors
     */
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

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
    public function errorMessage() {
        if($this->hasErrors()){
            $str ='';
            foreach ($this->errors as $e){
                $str .= $e ."<br>";                
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

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
}

<?php
namespace User\Domain\User;

use Application\Domain\Shared\AbstractEntity;
use Application\Domain\Shared\AggregateRootInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractUser extends AbstractEntity implements AggregateRootInterface
{

    protected $id;

    protected $token;

    protected $checksum;

    protected $title;

    protected $firstname;

    protected $lastname;

    protected $password;

    protected $salt;

    protected $email;

    protected $role;

    protected $registrationKey;

    protected $confirmed;

    protected $registerDate;

    protected $lastvisitDate;

    protected $block;

    protected $company;

    /**
     *
     * @param mixed $id
     */
    protected function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @param mixed $token
     */
    protected function setToken($token)
    {
        $this->token = $token;
    }

    /**
     *
     * @param mixed $checksum
     */
    protected function setChecksum($checksum)
    {
        $this->checksum = $checksum;
    }

    /**
     *
     * @param mixed $title
     */
    protected function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     *
     * @param mixed $firstname
     */
    protected function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     *
     * @param mixed $lastname
     */
    protected function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     *
     * @param mixed $password
     */
    protected function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     *
     * @param mixed $salt
     */
    protected function setSalt($salt)
    {
        $this->salt = $salt;
    }

    /**
     *
     * @param mixed $email
     */
    protected function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     *
     * @param mixed $role
     */
    protected function setRole($role)
    {
        $this->role = $role;
    }

    /**
     *
     * @param mixed $registrationKey
     */
    protected function setRegistrationKey($registrationKey)
    {
        $this->registrationKey = $registrationKey;
    }

    /**
     *
     * @param mixed $confirmed
     */
    protected function setConfirmed($confirmed)
    {
        $this->confirmed = $confirmed;
    }

    /**
     *
     * @param mixed $registerDate
     */
    protected function setRegisterDate($registerDate)
    {
        $this->registerDate = $registerDate;
    }

    /**
     *
     * @param mixed $lastvisitDate
     */
    protected function setLastvisitDate($lastvisitDate)
    {
        $this->lastvisitDate = $lastvisitDate;
    }

    /**
     *
     * @param mixed $block
     */
    protected function setBlock($block)
    {
        $this->block = $block;
    }

    /**
     *
     * @param mixed $company
     */
    protected function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     *
     * @return mixed
     */
    public function getChecksum()
    {
        return $this->checksum;
    }

    /**
     *
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     *
     * @return mixed
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     *
     * @return mixed
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     *
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     *
     * @return mixed
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     *
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     *
     * @return mixed
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     *
     * @return mixed
     */
    public function getRegistrationKey()
    {
        return $this->registrationKey;
    }

    /**
     *
     * @return mixed
     */
    public function getConfirmed()
    {
        return $this->confirmed;
    }

    /**
     *
     * @return mixed
     */
    public function getRegisterDate()
    {
        return $this->registerDate;
    }

    /**
     *
     * @return mixed
     */
    public function getLastvisitDate()
    {
        return $this->lastvisitDate;
    }

    /**
     *
     * @return mixed
     */
    public function getBlock()
    {
        return $this->block;
    }

    /**
     *
     * @return mixed
     */
    public function getCompany()
    {
        return $this->company;
    }
}
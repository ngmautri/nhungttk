<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MlaUsers
 *
 * @ORM\Table(name="mla_users", uniqueConstraints={@ORM\UniqueConstraint(name="CT_users_1", columns={"email"})}, indexes={@ORM\Index(name="mla_users_FK1_idx", columns={"company_id"})})
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Application\Repository\MlaUsersRepository")
 */
class MlaUsers
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=45, nullable=true)
     */
    private $token;
    
    /**
     * @var string
     *
     * @ORM\Column(name="checksum", type="string", length=45, nullable=true)
     */
    private $checksum;
    
    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=40, nullable=true)
     */
    private $title;
    
    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=64, nullable=false)
     */
    private $firstname = '';
    
    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=64, nullable=false)
     */
    private $lastname = '';
    
    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=32, nullable=false)
     */
    private $password = '';
    
    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=64, nullable=true)
     */
    private $salt;
    
    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    private $email = '';
    
    /**
     * @var string
     *
     * @ORM\Column(name="role", type="string", length=64, nullable=true)
     */
    private $role;
    
    /**
     * @var string
     *
     * @ORM\Column(name="registration_key", type="string", length=32, nullable=false)
     */
    private $registrationKey;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="confirmed", type="boolean", nullable=false)
     */
    private $confirmed = '0';
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="register_date", type="datetime", nullable=false)
     */
    private $registerDate = 'CURRENT_TIMESTAMP';
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastvisit_date", type="datetime", nullable=true)
     */
    private $lastvisitDate;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="block", type="boolean", nullable=false)
     */
    private $block = '0';
    
    /**
     * @var \Application\Entity\NmtApplicationCompany
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationCompany")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="company_id", referencedColumnName="id")
     * })
     */
    private $company;
    
    
    
    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Set token
     *
     * @param string $token
     *
     * @return MlaUsers
     */
    public function setToken($token)
    {
        $this->token = $token;
        
        return $this;
    }
    
    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }
    
    /**
     * Set checksum
     *
     * @param string $checksum
     *
     * @return MlaUsers
     */
    public function setChecksum($checksum)
    {
        $this->checksum = $checksum;
        
        return $this;
    }
    
    /**
     * Get checksum
     *
     * @return string
     */
    public function getChecksum()
    {
        return $this->checksum;
    }
    
    /**
     * Set title
     *
     * @param string $title
     *
     * @return MlaUsers
     */
    public function setTitle($title)
    {
        $this->title = $title;
        
        return $this;
    }
    
    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return MlaUsers
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
        
        return $this;
    }
    
    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }
    
    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return MlaUsers
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
        
        return $this;
    }
    
    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }
    
    /**
     * Set password
     *
     * @param string $password
     *
     * @return MlaUsers
     */
    public function setPassword($password)
    {
        $this->password = $password;
        
        return $this;
    }
    
    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }
    
    /**
     * Set salt
     *
     * @param string $salt
     *
     * @return MlaUsers
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
        
        return $this;
    }
    
    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }
    
    /**
     * Set email
     *
     * @param string $email
     *
     * @return MlaUsers
     */
    public function setEmail($email)
    {
        $this->email = $email;
        
        return $this;
    }
    
    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }
    
    /**
     * Set role
     *
     * @param string $role
     *
     * @return MlaUsers
     */
    public function setRole($role)
    {
        $this->role = $role;
        
        return $this;
    }
    
    /**
     * Get role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }
    
    /**
     * Set registrationKey
     *
     * @param string $registrationKey
     *
     * @return MlaUsers
     */
    public function setRegistrationKey($registrationKey)
    {
        $this->registrationKey = $registrationKey;
        
        return $this;
    }
    
    /**
     * Get registrationKey
     *
     * @return string
     */
    public function getRegistrationKey()
    {
        return $this->registrationKey;
    }
    
    /**
     * Set confirmed
     *
     * @param boolean $confirmed
     *
     * @return MlaUsers
     */
    public function setConfirmed($confirmed)
    {
        $this->confirmed = $confirmed;
        
        return $this;
    }
    
    /**
     * Get confirmed
     *
     * @return boolean
     */
    public function getConfirmed()
    {
        return $this->confirmed;
    }
    
    /**
     * Set registerDate
     *
     * @param \DateTime $registerDate
     *
     * @return MlaUsers
     */
    public function setRegisterDate($registerDate)
    {
        $this->registerDate = $registerDate;
        
        return $this;
    }
    
    /**
     * Get registerDate
     *
     * @return \DateTime
     */
    public function getRegisterDate()
    {
        return $this->registerDate;
    }
    
    /**
     * Set lastvisitDate
     *
     * @param \DateTime $lastvisitDate
     *
     * @return MlaUsers
     */
    public function setLastvisitDate($lastvisitDate)
    {
        $this->lastvisitDate = $lastvisitDate;
        
        return $this;
    }
    
    /**
     * Get lastvisitDate
     *
     * @return \DateTime
     */
    public function getLastvisitDate()
    {
        return $this->lastvisitDate;
    }
    
    /**
     * Set block
     *
     * @param boolean $block
     *
     * @return MlaUsers
     */
    public function setBlock($block)
    {
        $this->block = $block;
        
        return $this;
    }
    
    /**
     * Get block
     *
     * @return boolean
     */
    public function getBlock()
    {
        return $this->block;
    }
    
    /**
     * Set company
     *
     * @param \Application\Entity\NmtApplicationCompany $company
     *
     * @return MlaUsers
     */
    public function setCompany(\Application\Entity\NmtApplicationCompany $company = null)
    {
        $this->company = $company;
        
        return $this;
    }
    
    /**
     * Get company
     *
     * @return \Application\Entity\NmtApplicationCompany
     */
    public function getCompany()
    {
        return $this->company;
    }
}

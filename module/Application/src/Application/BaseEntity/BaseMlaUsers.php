<?php
namespace Application\BaseEntity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 *         ORM@MappedSuperclass
 */
class BaseMlaUsers
{

    /**
     * One product has many features.
     * This is the inverse side.
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\NmtApplicationAclUserRole", mappedBy="user")
     */
    protected $roleList;

    // ================================
    public function __construct()
    {
        $this->roleList = new ArrayCollection();
    }

    /**
     *
     * @return mixed
     */
    public function getRoleList()
    {
        return $this->roleList;
    }

    /**
     *
     * @param mixed $roleList
     */
    public function setRoleList($roleList)
    {
        $this->roleList = $roleList;
    }
}

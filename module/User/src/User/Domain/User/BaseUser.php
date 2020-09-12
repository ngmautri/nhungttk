<?php
namespace User\Domain\User;

use Application\Domain\Util\Tree\AbstractTree;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class BaseUser extends AbstractUser
{

    // addtional attribute.
    // =======================================
    protected $roleList;

    public function getRoleTree(AbstractTree $builder)
    {
        $builder->initTree();

        $trees = [];

        foreach ($this->roleList as $roleId) {
            $trees[] = $builder->createTree($roleId, 0);
        }

        return $trees;
    }

    public static function createSnapshotProps()
    {
        $baseClass = "User\Domain\User\BaseUser";
        $entity = new self();
        $reflectionClass = new \ReflectionClass($entity);

        $props = $reflectionClass->getProperties();

        foreach ($props as $property) {
            // echo $property->class . "\n";
            if ($property->class == $reflectionClass->getName() || $property->class == $baseClass) {
                $property->setAccessible(true);
                $propertyName = $property->getName();
                print "\n" . "public $" . $propertyName . ";";
            }
        }
    }

    public static function createSnapshotBaseProps()
    {
        $baseClass = "User\Domain\User\BaseUser";
        $entity = new self();
        $reflectionClass = new \ReflectionClass($entity);

        $props = $reflectionClass->getProperties();

        foreach ($props as $property) {
            // echo $property->class . "\n";
            if ($property->class != $baseClass) {
                $property->setAccessible(true);
                $propertyName = $property->getName();
                print "\n" . "public $" . $propertyName . ";";
            }
        }
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
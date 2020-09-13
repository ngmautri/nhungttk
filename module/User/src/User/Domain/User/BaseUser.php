<?php
namespace User\Domain\User;

use Application\Domain\Util\Tree\AbstractTree;
use Doctrine\Common\Collections\ArrayCollection;
use User\Domain\User\Repository\UserQueryRepositoryInterface;

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

    protected function getRoleTree(AbstractTree $builder)
    {
        $builder->initTree();

        $trees = [];

        foreach ($this->roleList as $roleId) {
            $trees[] = $builder->createTree($roleId, 0);
        }

        return $trees;
    }

    /**
     *
     * @param AbstractTree $builder
     * @return NULL|array
     */
    public function getParentRoles(AbstractTree $builder)
    {
        $trees = $this->getRoleTree($builder);
        if ($trees == null) {
            return null;
        }

        $parents = new ArrayCollection();
        foreach ($trees as $tree) {
            $paths = $tree->getPathId();
            if ($paths == null) {
                continue;
            }

            foreach ($paths as $p) {

                if (! $parents->contains($p)) {
                    $parents[] = $p;
                }
            }
        }

        return $parents->toArray();
    }

    public function isParentOf($userId, UserQueryRepositoryInterface $queryRep, AbstractTree $builder)
    {
        if ($queryRep == null || $userId == null || $builder == null) {
            return false;
        }

        if ($userId == $this->getId()) {
            return true;
        }

        $roles = $this->getRoleList();

        if ($roles == null) {
            return false;
        }

        /**
         *
         * @var GenericUser $otherUser
         */
        $otherUser = $queryRep->getById($userId);

        if ($otherUser == null) {
            return false;
        }

        $otherParents = $otherUser->getParentRoles($builder);

        if ($otherParents == null) {
            return false;
        }

        foreach ($roles as $r) {
            if (\in_array($r, $otherParents)) {
                return true;
            }
        }

        return false;
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
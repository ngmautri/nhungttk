<?php
namespace Application\Application\Specification\Zend;

use User\Infrastructure\Doctrine\UserQueryRepositoryImpl;
use User\Application\Service\ACLRole\Tree\ACLRoleTree;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class IsParentSpecification extends DoctrineSpecification
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecification::isSatisfiedBy()
     */
    public function isSatisfiedBy($subject)
    {
        $userId1 = null;
        if (isset($subject["userId1"])) {
            $userId1 = $subject["userId1"];
        }

        // var_dump($subject);

        $userId2 = null;
        if (isset($subject["userId2"])) {
            $userId2 = $subject["userId2"];
        }

        if ($this->doctrineEM == null || $userId2 == null || $userId1 == null) {
            return false;
        }

        $rep = new UserQueryRepositoryImpl($this->getDoctrineEM());
        $user1 = $rep->getById($userId1);
        if ($user1 == null) {
            return false;
        }

        $builder = new ACLRoleTree();
        $builder->setDoctrineEM($this->getDoctrineEM());
        return $user1->isParentOf($userId2, $rep, $builder);
    }
}
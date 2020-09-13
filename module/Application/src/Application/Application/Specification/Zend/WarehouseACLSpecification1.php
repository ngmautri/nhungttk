<?php
namespace Application\Application\Specification\Zend;

use User\Infrastructure\Persistence\UserRepositoryInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class WarehouseACLSpecification1 extends DoctrineSpecification
{

    private $userRepository;

    /**
     *
     * @return \User\Infrastructure\Persistence\UserRepositoryInterface
     */
    public function getUserRepository()
    {
        return $this->userRepository;
    }

    /**
     *
     * @param UserRepositoryInterface $userRepository
     */
    public function setUserRepository(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecification::isSatisfiedBy()
     */
    public function isSatisfiedBy($subject)
    {
        $warehouseId = null;
        if (isset($subject["warehouseId"])) {
            $warehouseId = $subject["warehouseId"];
        }

        $companyId = null;
        if (isset($subject["companyId"])) {
            $companyId = $subject["companyId"];
        }

        $userId = null;
        if (isset($subject["userId"])) {
            $userId = $subject["userId"];
        }

        if ($this->doctrineEM == null || $warehouseId == null || $companyId == null || $userId == null)
            return false;

        $criteria = array(
            "id" => $warehouseId,
            "company" => $companyId
        );

        /**
         *
         * @var \Application\Entity\NmtInventoryWarehouse $wh ;
         */
        $wh = $this->doctrineEM->getRepository("\Application\Entity\NmtInventoryWarehouse")->findOneBy($criteria);
        if ($wh == null)
            return false;

        if ($wh->getWhController() == null)
            return true;

        $spec = new IsParentSpecification($this->doctrineEM);
        $spec->setUserRepository($this->userRepository);

        $subject = array(
            "userId1" => $userId,
            "userId2" => $wh->getWhController()->getId()
        );
        return $spec->isSatisfiedBy($subject);
    }

    /**
     *
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     *
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }
}
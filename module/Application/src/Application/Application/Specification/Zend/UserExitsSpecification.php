<?php
namespace Application\Application\Specification\Zend;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class UserExitsSpecification extends DoctrineSpecification
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecification::isSatisfiedBy()
     */
    public function isSatisfiedBy($subject)
    {
        $userId = null;
        if (isset($subject["userId"])) {
            $userId = $subject["userId"];
        }

        if ($this->doctrineEM == null || $userId == null) {
            return false;
        }

        $criteria = array(
            "id" => $userId
        );

        /**
         *
         * @var \Application\Entity\MlaUsers $entiy ;
         */
        $entiy = $this->doctrineEM->getRepository("\Application\Entity\MlaUsers")->findOneBy($criteria);
        return (! $entiy == null);
    }
}
<?php
namespace Application\Application\Specification\Zend;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class IncotermSpecification extends DoctrineSpecification
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecification::isSatisfiedBy()
     */
    public function isSatisfiedBy($subject)
    {
        $incotermId = null;
        if (isset($subject["incotermId"])) {
            $incotermId = $subject["incotermId"];
        }

        if ($this->doctrineEM == null || $incotermId == null) {
            return false;
        }

        $criteria = array(
            "id" => $incotermId
        );

        /**
         *
         * @var \Application\Entity\NmtApplicationIncoterms $doctrineEntity ;
         */
        $doctrineEntity = $this->doctrineEM->getRepository("\Application\Entity\NmtApplicationIncoterms")->findOneBy($criteria);
        return (! $doctrineEntity == null);
    }
}
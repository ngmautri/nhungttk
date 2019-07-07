<?php
namespace Application\Infrastructure\AggregateRepository;

use Application\Domain\Exception\InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AbstractDoctrineRepository
{

    protected $doctrineEM;

    public function __construct(\Doctrine\ORM\EntityManager $doctrineEM)
    {
        if ($doctrineEM == null)
            throw new InvalidArgumentException("Entitiy manager not found.");

        $this->doctrineEM = $doctrineEM;
    }

    /**
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     *
     * @param object $obj
     * @return string|NULL
     */
    public function generateSysNumber($obj)
    {
        $criteria = array(
            'isActive' => 1,
            'subjectClass' => get_class($obj)
        );

        /** @var \Application\Entity\NmtApplicationDocNumber $docNumber ; */
        $docNumber = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationDocNumber')->findOneBy($criteria);

        if ($docNumber instanceof \Application\Entity\NmtApplicationDocNumber) {

            $maxLen = strlen($docNumber->getToNumber());
            $currentLen = 1;
            $currentDoc = $docNumber->getPrefix();
            $current_no = $docNumber->getCurrentNumber();

            if ($current_no == null) {
                $current_no = $docNumber->getFromNumber();
            } else {
                $current_no ++;
                $currentLen = strlen($current_no);
            }

            $docNumber->setCurrentNumber($current_no);

            $tmp = "";
            for ($i = 0; $i < $maxLen - $currentLen; $i ++) {

                $tmp = $tmp . "0";
            }

            $currentDoc = $currentDoc . $tmp . $current_no;
            return $currentDoc;
        }

        return null;
    }
}

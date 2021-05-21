<?php
namespace Application\Infrastructure\Persistence\Domain\Doctrine;

use Application\Domain\Company\AccountChart\GenericAccount;
use Application\Domain\Company\AccountChart\Factory\ChartFactory;
use Application\Domain\Company\AccountChart\Repository\ChartQueryRepositoryInterface;
use Application\Domain\Contracts\Repository\CompanySqlFilterInterface;
use Application\Entity\AppCoaAccount;
use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Application\Infrastructure\Persistence\Domain\Doctrine\Mapper\ChartMapper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ChartQueryRepositoryImpl extends AbstractDoctrineRepository implements ChartQueryRepositoryInterface
{

    const CHART_ENTITY_NAME = "\Application\Entity\AppCoa";

    const ACCOUNT_ENTITY_NAME = "\Application\Entity\AppCoaAccount";

    public function getRootByMemberId($memberId)
    {
        $criteria = array(
            'id' => $memberId
        );

        $doctrineEntity = $this->doctrineEM->getRepository(self::CHART_ENTITY_NAME)->findOneBy($criteria);
        if ($doctrineEntity == null) {
            throw new \InvalidArgumentException("Not Found id" . $memberId);
        }

        if ($doctrineEntity->getPr() != null) {
            return $doctrineEntity->getPr()->getId();
        }

        return null;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\AccountChart\Repository\ChartQueryRepositoryInterface::getList()
     */
    public function getList(CompanySqlFilterInterface $filter)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\AccountChart\Repository\ChartQueryRepositoryInterface::getById()
     */
    public function getById($id)
    {
        if ($id == null) {
            return null;
        }

        $criteria = array(
            'id' => $id
        );

        $rootEntityDoctrine = $this->getDoctrineEM()
            ->getRepository('\Application\Entity\AppCoa')
            ->findOneBy($criteria);

        if ($rootEntityDoctrine == null) {
            return null;
        }

        $rootSnapshot = ChartMapper::createChartSnapshot($rootEntityDoctrine);
        $rootEntity = ChartFactory::contructFromDB($rootSnapshot);

        $rootEntity->setAccountCollectionRef($this->_createAccountCollectionRef($id));
        return $rootEntity;
    }

    public function getByUUID($uuid)
    {}

    /**
     *
     * @param int $id
     * @return Callable
     */
    private function _createAccountCollectionRef($id)
    {
        return function () use ($id) {

            $criteria = [
                'coa' => $id
            ];
            $results = $this->getDoctrineEM()
                ->getRepository('\Application\Entity\AppCoaAccount')
                ->findBy($criteria);

            $collection = new ArrayCollection();

            if (count($results) == 0) {
                return $collection;
            }

            foreach ($results as $r) {

                /**@var AppCoaAccount $localEnityDoctrine ;*/
                $localEnityDoctrine = $r;
                $snapshot = ChartMapper::createAccountSnapshot($localEnityDoctrine);
                $collection->add(GenericAccount::constructFromDB($snapshot));
            }
            return $collection;
        };
    }

    /**
     *
     * @param int $id
     * @return array|NULL
     */
    private function _getMembersOf($id)
    {
        try {
            $criteria = [
                'coa' => $id
            ];
            $results = $this->getDoctrineEM()
                ->getRepository('\Application\Entity\AppCoaAccount')
                ->findAll($criteria);

            return $results;
        } catch (NoResultException $e) {
            return null;
        }
    }

    private function _getMembersByRootId($id)
    {
        $sql = "";

        $sql = sprintf($sql, $id);

        // echo $sql;
        try {
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\FinVendorInvoiceRow', 'fin_vendor_invoice_row');
            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            return $query->getResult();
        } catch (NoResultException $e) {
            return null;
        }
    }
}
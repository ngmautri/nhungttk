<?php
namespace Application\Infrastructure\Persistence\Domain\Doctrine;

use Application\Domain\Company\CompanyQueryRepositoryInterface;
use Application\Domain\Company\AccountChart\Factory\ChartFactory;
use Application\Domain\Company\Collection\AccountChartCollection;
use Application\Domain\Company\Collection\WarehouseCollection;
use Application\Domain\Company\Factory\CompanyFactory;
use Application\Domain\Contracts\Repository\SqlFilterInterface;
use Application\Entity\AppCoa;
use Application\Entity\AppCoaAccount;
use Application\Entity\NmtApplicationDepartment;
use Application\Entity\NmtFinPostingPeriod;
use Application\Entity\NmtInventoryWarehouse;
use Application\Entity\NmtInventoryWarehouseLocation;
use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Application\Infrastructure\Mapper\CompanyMapper;
use Application\Infrastructure\Persistence\Domain\Doctrine\Filter\CompanyQuerySqlFilter;
use Application\Infrastructure\Persistence\Domain\Doctrine\Helper\CompanyHelper;
use Application\Infrastructure\Persistence\Domain\Doctrine\Mapper\ChartMapper;
use Application\Infrastructure\Persistence\Domain\Doctrine\Mapper\DepartmentMapper;
use Doctrine\Common\Collections\ArrayCollection;
use Inventory\Domain\Warehouse\Factory\WarehouseFactory;
use Inventory\Infrastructure\Mapper\WhMapper;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class CompanyQueryRepositoryImpl extends AbstractDoctrineRepository implements CompanyQueryRepositoryInterface
{

    public function getPostingPeriod($periodId)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\CompanyQueryRepositoryInterface::getById()
     */
    public function getById($id)
    {
        $criteria = array(
            "id" => $id
        );

        /**
         *
         * @var \Application\Entity\NmtApplicationCompany $entity ;
         */
        $entity = $this->doctrineEM->getRepository("\Application\Entity\NmtApplicationCompany")->findOneBy($criteria);

        Assert::notNull($entity, "NmtApplicationCompany not found!");
        $snapshot = CompanyMapper::createSnapshot($entity, $this->getDoctrineEM());
        $entityRoot = CompanyFactory::contructFromDB($snapshot);

        Assert::notNull($entityRoot);
        $entityRoot->setDepartmentCollectionRef($this->_createDepartmentCollectionRef($id));
        $entityRoot->setAccountChartCollectionRef($this->_createAccountChartCollectionRef($id));
        $entityRoot->setPostingPeriodCollectionRef($this->_createPostingPeriodCollectionRef($id));
        $entityRoot->setWarehouseCollectionRef($this->_createWarehouseCollectionRef($id));
        return $entityRoot;
    }

    public function getByUUID($uuid)
    {}

    public function findAll()
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\Repository\CompanyQueryRepositoryInterface::getDepartmentList()
     */
    public function getDepartmentList(SqlFilterInterface $filter)
    {
        $sort_by = null;
        $sort = null;
        $limit = null;
        $offset = null;
        $results = CompanyHelper::getDepartmentList($this->getDoctrineEM(), $filter, $sort_by, $sort, $limit, $offset);

        if ($results == null) {
            return null;
        }
        $tmp = [];
        foreach ($results as $r) {
            $tmp[] = DepartmentMapper::createSnapshot($r);
        }
        return $tmp;
    }

    // ===================================================
    private function _createDepartmentCollectionRef($id)
    {
        return function () use ($id) {

            $filter = new CompanyQuerySqlFilter();
            $filter->setCompanyId($id);
            $results = CompanyHelper::getDepartments($this->getDoctrineEM(), $filter);
            $collection = new ArrayCollection();

            if (count($results) == 0) {
                return $collection;
            }

            foreach ($results as $r) {

                /**@var NmtApplicationDepartment $localEnityDoctrine ;*/
                $localEnityDoctrine = $r;
                $localSnapshot = DepartmentMapper::createSnapshot($localEnityDoctrine);
                $collection->add($localSnapshot);
            }
            return $collection;
        };
    }

    private function _createAccountChartCollectionRef($id)
    {
        return function () use ($id) {

            $filter = new CompanyQuerySqlFilter();
            $filter->setCompanyId($id);
            $results = CompanyHelper::getAccountCharts($this->getDoctrineEM(), $filter);

            $collection = new AccountChartCollection();

            if (count($results) == 0) {
                return $collection;
            }

            foreach ($results as $r) {

                /**@var AppCoa $localEnityDoctrine ;*/
                $localEnityDoctrine = $r;
                $localSnapshot = ChartMapper::createChartSnapshot($localEnityDoctrine);
                $chart = ChartFactory::contructFromDB($localSnapshot);

                // $chart->setAccountCollectionRef($this->_createAccountCollectionRef($chart->getId()));
                $collection->add($chart);
            }
            return $collection;
        };
    }

    private function _createWarehouseCollectionRef($id)
    {
        return function () use ($id) {

            $criteria = [
                'company' => $id
            ];
            $results = $this->getDoctrineEM()
                ->getRepository('\Application\Entity\NmtInventoryWarehouse')
                ->findBy($criteria);

            $collection = new WarehouseCollection();

            if (count($results) == 0) {
                return $collection;
            }

            foreach ($results as $r) {

                /**@var NmtInventoryWarehouse $localEnityDoctrine ;*/
                $localEnityDoctrine = $r;
                $localSnapshot = WhMapper::createSnapshot($this->getDoctrineEM(), $localEnityDoctrine);
                $wh = WarehouseFactory::contructFromDB($localSnapshot);
                $wh->setLocationCollectionRef($this->_createLocationCollectionRef($wh->getId()));
                $collection->add($wh);
            }
            return $collection;
        };
    }

    private function _createLocationCollectionRef($id)
    {
        return function () use ($id) {

            $criteria = [
                'warehouse' => $id
            ];
            $results = $this->getDoctrineEM()
                ->getRepository('\Application\Entity\NmtInventoryWarehouseLocation')
                ->findBy($criteria);

            $collection = new ArrayCollection();

            if (count($results) == 0) {
                return $collection;
            }

            foreach ($results as $r) {

                /**@var NmtInventoryWarehouseLocation $localEnityDoctrine ;*/
                $localEnityDoctrine = $r;
                $localSnapshot = WhMapper::createLocationSnapshot($this->getDoctrineEM(), $localEnityDoctrine);
                $collection->add($localSnapshot);
            }
            return $collection;
        };
    }

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
                $localSnapshot = ChartMapper::createAccountSnapshot($localEnityDoctrine);
                $collection->add($localSnapshot);
            }
            return $collection;
        };
    }

    private function _createPostingPeriodCollectionRef($id)
    {
        return function () use ($id) {

            $filter = new CompanyQuerySqlFilter();
            $filter->setCompanyId($id);
            $results = CompanyHelper::getPostingPeriods($this->getDoctrineEM(), $filter);

            $collection = new ArrayCollection();

            if (count($results) == 0) {
                return $collection;
            }

            foreach ($results as $r) {

                /**@var NmtFinPostingPeriod $localEnityDoctrine ;*/
                $localEnityDoctrine = $r;
                $collection->add($localEnityDoctrine);
            }
            return $collection;
        };
    }
}

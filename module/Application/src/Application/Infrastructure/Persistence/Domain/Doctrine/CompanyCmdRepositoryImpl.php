<?php
namespace Application\Infrastructure\Persistence\Domain\Doctrine;

use Application\Domain\Company\GenericCompany;
use Application\Domain\Company\Department\DepartmentSnapshot;
use Application\Domain\Company\Repository\CompanyCmdRepositoryInterface;
use Application\Entity\NmtApplicationCompany;
use Application\Entity\NmtApplicationDepartment;
use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Application\Infrastructure\Persistence\Domain\Doctrine\Mapper\DepartmentMapper;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class CompanyCmdRepositoryImpl extends AbstractDoctrineRepository implements CompanyCmdRepositoryInterface
{

    const ROOT_ENTITY_NAME = "\Application\Entity\NmtApplicationCompany";

    const DEPT_ENTITY_NAME = "\Application\Entity\NmtApplicationDepartment";

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\Repository\CompanyCmdRepositoryInterface::removeDepartment()
     */
    public function removeDepartment(GenericCompany $company, $department)
    {
        if ($company == null) {
            throw new InvalidArgumentException("Root entity not given.");
        }

        /**
         *
         * @var DepartmentSnapshot $localSnapshot ;
         */
        $localSnapshot = $department;

        /**
         *
         * @var NmtApplicationCompany $rootEntityDoctrine ;
         */
        $rootEntityDoctrine = $this->getDoctrineEM()->find(self::ROOT_ENTITY_NAME, $company->getId());

        if ($rootEntityDoctrine == null) {
            throw new InvalidArgumentException("Doctrine root entity not found.");
        }

        /**
         *
         * @var NmtApplicationDepartment $rowEntityDoctrine ;
         */
        $rowEntityDoctrine = $this->getDoctrineEM()->find(self::DEPT_ENTITY_NAME, $localSnapshot->getNodeId());

        if ($rowEntityDoctrine == null) {
            throw new InvalidArgumentException(sprintf("Doctrine row entity not found! #%s", $localSnapshot->getNodeId()));
        }

        //
        if ($rowEntityDoctrine->getCompany() == null) {
            throw new InvalidArgumentException("Doctrine row entity is not valid");
        }

        if ($rowEntityDoctrine->getCompany()->getId() != $rootEntityDoctrine->getId()) {
            throw new InvalidArgumentException(sprintf("Doctrine row entity is corrupted! %s <> %s ", $rowEntityDoctrine->getInvoice()->getId(), $localSnapshot->getNodeId()));
        }

        $isFlush = true;
        $increaseVersion = false;

        // remove now.
        $this->getDoctrineEM()->remove($rowEntityDoctrine);

        if ($increaseVersion) {
            $rootEntityDoctrine->setRevisionNo($rootEntityDoctrine->getRevisionNo() + 1);
            $this->doctrineEM->persist($rootEntityDoctrine);
        }

        if ($isFlush) {
            $this->doctrineEM->flush();
        }

        return true;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\Repository\CompanyCmdRepositoryInterface::storeDeparment()
     */
    public function storeDeparment(GenericCompany $company, $department)
    {
        if ($company == null) {
            throw new \InvalidArgumentException("Company not given.");
        }

        if ($department == null) {
            throw new InvalidArgumentException("Deparment snapshot is not given!");
        }

        /**
         *
         * @var DepartmentSnapshot $localSnapshot ;
         */
        $localSnapshot = $department;

        $rootEntityDoctrine = $this->getDoctrineEM()->find(self::ROOT_ENTITY_NAME, $company->getId());

        if (! $rootEntityDoctrine instanceof NmtApplicationCompany) {
            throw new InvalidArgumentException("Doctrine root entity not given!");
        }

        $isFlush = true;
        $increaseVersion = FALSE;

        /**
         *
         * @var \Application\Entity\NmtApplicationDepartment $rowEntityDoctrine ;
         */

        if ($localSnapshot->getNodeId() > 0) {

            $rowEntityDoctrine = $this->doctrineEM->find(self::DEPT_ENTITY_NAME, $localSnapshot->getNodeId());

            if ($rowEntityDoctrine == null) {
                throw new InvalidArgumentException(sprintf("Doctrine row entity not found! #%s", $localSnapshot->getNodeId()));
            }

            // to update
            if ($rowEntityDoctrine->getCompany() == null) {
                throw new InvalidArgumentException("Doctrine row entity is not valid");
            }

            // to update
            if (! $rowEntityDoctrine->getCompany()->getId() == $rootEntityDoctrine->getId()) {
                throw new InvalidArgumentException(sprintf("Doctrine row entity is corrupted! %s <> %s ", $rowEntityDoctrine->getGr()->getId(), $rootEntityDoctrine->getId()));
            }
        } else {
            $localClassName = self::DEPT_ENTITY_NAME;
            $rowEntityDoctrine = new $localClassName();

            // to update
            $rowEntityDoctrine->setCompany($rootEntityDoctrine);
            $rowEntityDoctrine->setNodeParentId($localSnapshot->getNodeParentId());
        }

        $rowEntityDoctrine = DepartmentMapper::mapSnapshotEntity($this->getDoctrineEM(), $localSnapshot, $rowEntityDoctrine);

        $this->doctrineEM->persist($rowEntityDoctrine);

        if ($increaseVersion) {
            $rootEntityDoctrine->setRevisionNo($rootEntityDoctrine->getRevisionNo() + 1);
            $this->doctrineEM->persist($rootEntityDoctrine);
        }

        if ($isFlush) {
            $this->doctrineEM->flush();
        }

        return $rowEntityDoctrine;

        if ($rowEntityDoctrine == null) {
            throw new \RuntimeException("Something wrong. Row Doctrine Entity not created");
        }

        return $localSnapshot;
    }

    public function store(GenericCompany $company)
    {}

    public function storeWarehouse(GenericCompany $company, $warehouse)
    {}

    public function storePostingPeriod(GenericCompany $company)
    {}
}

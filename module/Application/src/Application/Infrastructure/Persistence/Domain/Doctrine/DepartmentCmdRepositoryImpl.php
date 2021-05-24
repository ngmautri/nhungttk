<?php
namespace Application\Infrastructure\Persistence\Domain\Doctrine;

use Application\Domain\Company\BaseCompany;
use Application\Domain\Company\Department\BaseDepartment;
use Application\Domain\Company\Department\DepartmentSnapshot;
use Application\Domain\Company\Department\Repository\DepartmentCmdRepositoryInterface;
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
class DepartmentCmdRepositoryImpl extends AbstractDoctrineRepository implements DepartmentCmdRepositoryInterface
{

    const COMPANNY_ENTITY_NAME = "\Application\Entity\NmtApplicationCompany";

    const DEPARTMENT_ENTITY_NAME = "\Application\Entity\NmtApplicationDepartment";

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\Department\Repository\DepartmentCmdRepositoryInterface::store()
     */
    public function store(BaseDepartment $rootEntity, $isPosting = false)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\Department\Repository\DepartmentCmdRepositoryInterface::remove()
     */
    public function remove(BaseDepartment $rootEntity, $isPosting = false)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\Department\Repository\DepartmentCmdRepositoryInterface::storeDepartment()
     */
    public function removeDepartment(BaseCompany $rootEntity, DepartmentSnapshot $localSnapshot, $isPosting = false)
    {
        $rootEntityDoctrine = $this->assertAndReturnCompany($rootEntity);
        $rowEntityDoctrine = $this->assertAndReturnDepartment($rootEntityDoctrine, $localSnapshot);

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
     * @see \Application\Domain\Company\Department\Repository\DepartmentCmdRepositoryInterface::removeDepartment()
     */
    public function storeDepartment(BaseCompany $rootEntity, DepartmentSnapshot $localSnapshot, $isPosting = false)
    {
        $rootEntityDoctrine = $this->assertAndReturnCompany($rootEntity);
        $rowEntityDoctrine = $this->assertAndReturnDepartment($rootEntityDoctrine, $localSnapshot);

        $isFlush = true;
        $increaseVersion = FALSE;

        $rowEntityDoctrine = DepartmentMapper::mapSnapshotEntity($this->getDoctrineEM(), $localSnapshot, $rowEntityDoctrine);

        $this->doctrineEM->persist($rowEntityDoctrine);

        if ($increaseVersion) {
            $rootEntityDoctrine->setRevisionNo($rootEntityDoctrine->getRevisionNo() + 1);
            $this->doctrineEM->persist($rootEntityDoctrine);
        }

        if ($isFlush) {
            $this->doctrineEM->flush();
        }

        if ($rowEntityDoctrine == null) {
            throw new \RuntimeException("Something wrong. Row Doctrine Entity not created");
        }

        return $localSnapshot;
    }

    /**
     *
     * @param BaseCompany $rootEntity
     * @throws InvalidArgumentException
     */
    private function assertAndReturnCompany(BaseCompany $rootEntity)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("BaseCompany not given.");
        }

        /**
         *
         * @var NmtApplicationCompany $rowEntityDoctrine ;
         */
        $rootEntityDoctrine = $this->getDoctrineEM()->find(DepartmentCmdRepositoryImpl::COMPANNY_ENTITY_NAME, $rootEntity->getId());
        if (! $rootEntityDoctrine instanceof NmtApplicationCompany) {
            throw new InvalidArgumentException("Doctrine root entity not given!");
        }

        return $rootEntityDoctrine;
    }

    /**
     *
     * @param NmtApplicationCompany $rootEntityDoctrine
     * @param DepartmentSnapshot $localSnapshot
     * @throws InvalidArgumentException
     * @return \Application\Entity\NmtApplicationDepartment
     */
    private function assertAndReturnDepartment(NmtApplicationCompany $rootEntityDoctrine, DepartmentSnapshot $localSnapshot)
    {
        $rowEntityDoctrine = null;

        if ($localSnapshot->getNodeId() > 0) {

            /**
             *
             * @var NmtApplicationDepartment $rowEntityDoctrine ;
             */
            $rowEntityDoctrine = $this->doctrineEM->find(DepartmentCmdRepositoryImpl::DEPARTMENT_ENTITY_NAME, $localSnapshot->getNodeId());

            if ($rowEntityDoctrine == null) {
                throw new InvalidArgumentException(sprintf("Department row entity not found! #%s", $localSnapshot->getNodeId()));
            }

            // to update
            if ($rowEntityDoctrine->getCompany() == null) {
                throw new InvalidArgumentException("Department row entity is not valid");
            }

            // to update
            if (! $rowEntityDoctrine->getCompany()->getId() == $rootEntityDoctrine->getId()) {
                throw new InvalidArgumentException(sprintf("Department row entity is corrupted! %s <> %s ", $rowEntityDoctrine->getGr()->getId(), $rootEntityDoctrine->getId()));
            }
        } else {
            $localClassName = DepartmentCmdRepositoryImpl::DEPARTMENT_ENTITY_NAME;
            $rowEntityDoctrine = new $localClassName();

            // to update
            $rowEntityDoctrine->setCompany($rootEntityDoctrine);
            $rowEntityDoctrine->setNodeParentId($localSnapshot->getNodeParentId());
        }

        if ($rowEntityDoctrine == null) {
            throw new InvalidArgumentException("Can not create department entity!");
        }

        return $rowEntityDoctrine;
    }
}

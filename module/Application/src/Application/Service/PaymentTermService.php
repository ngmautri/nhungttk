<?php
namespace Application\Service;

/**
 * Payment Term Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PaymentTermService extends AbstractService
{

    /**
     *
     * @param \Application\Entity\NmtApplicationPmtTerm $entity
     * @param array $data
     * @/param boolean $isNew
     */
    public function validateHeader(\Application\Entity\NmtApplicationPmtTerm $entity, $data, $isNew = FALSE)
    {
        $errors = array();

        if (! $entity instanceof \Application\Entity\NmtApplicationPmtTerm) {
            $errors[] = $this->controllerPlugin->translate('Payment term is not found!');
        }

        if ($data == null) {
            $errors[] = $this->controllerPlugin->translate('No input given');
        }

        if (count($errors) > 0) {
            return $errors;
        }

        $pmtTermCode = $data['pmtTermCode'];
        $pmtTermName = $data['pmtTermName'];
        $isPrepayment = (int) $data['isPrepayment'];
        $isActive = (int) $data['isActive'];

        $description = $data['description'];

        $r = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationPmtTerm')->findBy(array(
            'pmtTermCode' => $pmtTermCode
        ));

       

        if ($pmtTermCode == "") {
            $errors[] = $this->controllerPlugin->translate('Payment term code is not correct or empty!');
        } else {
            
            if ($isNew == TRUE) {
                if (count($r) >= 1) {
                    $errors[] = $pmtTermCode . ' exists already';
                }
            }              
            $entity->setPmtTermCode($pmtTermCode);
        }

        if ($pmtTermName == "") {
            $errors[] = $this->controllerPlugin->translate('Payment term name is not correct or empty!');
        } else {
            $entity->setPmtTermName($pmtTermName);
        }

        $entity->setIsPrepayment($isPrepayment);
        $entity->setIsActive($isActive);
        $entity->setDescription($description);

        return $errors;
    }

    /**
     *
     * @param \Application\Entity\NmtApplicationPmtTerm $entity
     * @param \Application\Entity\MlaUsers $u
     * @param array $data
     * @param boolean $isNew
     */
    public function saveHeader($entity, $data, $u, $isNew = FALSE)
    {
        $errors = array();

        if ($u == null) {
            $m = $this->controllerPlugin->translate("Invalid Argument! User can't be indentided for this transaction.");
            throw new \Exception($m);
        }

        if (! $entity instanceof \Application\Entity\NmtApplicationPmtTerm) {
            $m = $this->controllerPlugin->translate("Invalid Argument! Payment term Object not found!");
            throw new \Exception($m);
        }

        // validated.
        $oldEntity = clone ($entity);
        

        $ck = $this->validateHeader($entity, $data, $isNew);

        if (count($ck) > 0) {
            return $ck;
        }

        $changeOn = new \DateTime();

        if ($isNew == TRUE) {

            $entity->setCreatedBy($u);
            $entity->setCreatedOn($changeOn);
            $m = sprintf('[OK] Payment Term #%s created.', $entity->getId());
        } else {

      
            $changeArray = $this->controllerPlugin->objectsAreIdentical($oldEntity, $entity);
            if (count($changeArray) == 0) {
                $errors[] = sprintf('Nothing changed!');
                return $errors;
            }

            $entity->setRevisionNo($entity->getRevisionNo() + 1);
            $entity->setLastchangeBy($u);
            $entity->setLastchangeOn($changeOn);
            $m = sprintf('[OK] Payment Term #%s updated.', $entity->getId());

            $this->getEventManager()->trigger('application.change.log', __METHOD__, array(
                'priority' => 7,
                'message' => $m,
                'objectId' => $entity->getId(),
                'objectToken' => $entity->getToken(),
                'changeArray' => $changeArray,
                'changeBy' => $u,
                'changeOn' => $changeOn,
                'revisionNumber' => $entity->getRevisionNo(),
                'changeDate' => $changeOn,
                'changeValidFrom' => $changeOn
            ));
        }

        $this->doctrineEM->persist($entity);
        $this->doctrineEM->flush();

        $this->getEventManager()->trigger('application.activity.log', __METHOD__, array(
            'priority' => \Zend\Log\Logger::INFO,
            'message' => $m,
            'createdBy' => $u,
            'createdOn' => $changeOn
        ));
    }
}

<?php
namespace Application\Service;

/**
 * Payment Method Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PmtMethodService extends AbstractService
{

    /**
     *
     * @param \Application\Entity\NmtApplicationPmtMethod $entity
     * @param array $data
     * @/param boolean $isPosting
     */
    public function validateHeader(\Application\Entity\NmtApplicationPmtMethod $entity, $data)
    {
        $errors = array();

        if (! $entity instanceof \Application\Entity\NmtApplicationPmtMethod) {
            $errors[] = $this->controllerPlugin->translate('Payment Mehod is not found!');
        }

        if ($data == null) {
            $errors[] = $this->controllerPlugin->translate('No input given');
        }

        if (count($errors) > 0) {
            return $errors;
        }

        $methodCode = $data['methodCode'];
        $methodName = $data['methodName'];
        $description = $data['description'];

        if ($methodCode == "") {
            $errors[] = $this->controllerPlugin->translate('Method Code is not correct or empty!');
        } else {
            $entity->setMethodCode($methodCode);
        }

        if ($methodName == "") {
            $errors[] = $this->controllerPlugin->translate('Method Name is not correct or empty!');
        } else {
            $entity->setMethodName($methodName);
        }

        $entity->setDescription($description);

        return $errors;
    }

    /**
     *
     * @param \Application\Entity\NmtApplicationPmtMethod $entity
     * @param \Application\Entity\MlaUsers $u
     * @param boolean $isNew
     */
    public function saveHeader($entity, $u, $isNew = FALSE)
    {
        if ($u == null) {
            $m = $this->controllerPlugin->translate("Invalid Argument! User can't be indentided for this transaction.");
            throw new \Exception($m);
        }

        if (! $entity instanceof \Application\Entity\NmtApplicationPmtMethod) {
            $m = $this->controllerPlugin->translate("Invalid Argument! Payment Method Object not found!");
            throw new \Exception($m);
        }

        // validated.

        $changeOn = new \DateTime();

        if ($isNew == TRUE) {

            $entity->setCreatedBy($u);
            $entity->setCreatedOn($changeOn);
        } else {
            $entity->setRevisionNo($entity->getRevisionNo() + 1);
            $entity->setLastchangeBy($u);
            $entity->setLastchangeOn($changeOn);
        }

        $this->doctrineEM->persist($entity);
        $this->doctrineEM->flush();
    }
}

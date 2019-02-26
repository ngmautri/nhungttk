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
     * @param \Application\Entity\NmtProcurePo $entity
     * @param array $data
     * @/param boolean $isPosting
     */
    public function validateHeader(\Application\Entity\NmtApplicationIncoterms $entity, $data)
    {
        $errors = array();

        if (! $entity instanceof \Application\Entity\NmtApplicationIncoterms) {
            $errors[] = $this->controllerPlugin->translate('Incoterm is not found!');
        }

        if ($data == null) {
            $errors[] = $this->controllerPlugin->translate('No input given');
        }

        if (count($errors) > 0) {
            return $errors;
        }

        $incoterm = $data['incoterm'];
        $incoterm1 = $data['incoterm1'];
        $locationRequired = $data['locationRequired'];
        
        $incotermDescription = $data['incotermDescription'];

        if ($incoterm == "") {
            $errors[] = $this->controllerPlugin->translate('Incoterm is not correct or empty!');
        } else {
            $entity->setIncoterm($incoterm);
        }
        
        if ($incoterm1 == "") {
            $errors[] = $this->controllerPlugin->translate('Incoterm is not correct or empty!');
        } else {
            $entity->setIncoterm1($incoterm1);
        }

        $entity->setLocationRequired($locationRequired);
        $entity->setIncotermDescription($incotermDescription);

        return $errors;
    }

    /**
     *
     * @param \Application\Entity\NmtApplicationIncoterms $entity
     * @param \Application\Entity\MlaUsers $u
     * @param boolean $isNew
     */
    public function saveHeader($entity, $u, $isNew = FALSE)
    {
        if ($u == null) {
            $m = $this->controllerPlugin->translate("Invalid Argument! User can't be indentided for this transaction.");
            throw new \Exception($m);
        }

        if (! $entity instanceof \Application\Entity\NmtApplicationIncoterms) {
            $m = $this->controllerPlugin->translate("Invalid Argument! Incoterm Object not found!");
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

<?php
namespace Payment\Service;


/**
 * AP Payment Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface PaymentInterface
{

    public function validateHeader(\Application\Entity\PmtOutgoing $entity, $data, $isPosting = TRUE);

    public function saveHeader($entity, array $data, $u, $isNew = FALSE);

    public function post($entity, array $data, $u, $isNew = TRUE, $isFlush = false);

    public function reverse($entity, $u, $reversalDate, $reversalReason);
}

<?php
namespace Procure\Application\Command\PO\Options;

use Procure\Domain\Exception\PoCreateException;
use Application\Domain\Shared\Command\CommandOptions;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PoCreateOptions implements CommandOptions
{

    private $companyId;

    private $userId;

    private $triggeredBy;

    /**
     *
     * @param int $companyId
     * @param int $userId
     * @param string $triggeredBy
     */
    public function __construct($companyId, $userId, $triggeredBy)
    {
        if ($companyId == null) {
            throw new PoCreateException(sprintf("Company ID not given! %s", $companyId));
        }

        if ($userId == null) {
            throw new PoCreateException(sprintf("User ID not given! %s", $userId));
        }

        if ($triggeredBy == null || $triggeredBy == "") {
            throw new PoCreateException(sprintf("Trigger not given! %s", $userId));
        }

        $this->companyId = $companyId;
        $this->userId = $userId;
        $this->triggeredBy = $triggeredBy;
    }

    /**
     *
     * @return mixed
     */
    public function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     *
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     *
     * @return mixed
     */
    public function getTriggeredBy()
    {
        return $this->triggeredBy;
    }
}

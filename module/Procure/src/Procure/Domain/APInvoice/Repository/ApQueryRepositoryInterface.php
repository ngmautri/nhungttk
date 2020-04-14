<?php
namespace Procure\Domain\APInvoice\Repository;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface ApQueryRepositoryInterface
{

    /**
     *
     * @param int $id
     */
    public function getHeaderIdByRowId($id);

    /**
     *
     * @param int $id
     * @param string $token
     */
    public function getVersion($id, $token = null);

    /**
     *
     * @param int $id
     * @param string $token
     */
    public function getVersionArray($id, $token = null);
}
<?php
namespace Inventory\Domain\Transaction;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TrxDoc extends GenericTrx
{

    // Specific Attribute, if any
    // =========================

    // ==========================
    private static $instance = null;

    private function __construct()
    {}

    /**
     *
     * @return \Inventory\Domain\Transaction\TrxDoc
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new TrxDoc();
        }
        return self::$instance;
    }

    public function specify()
    {}

    public function refreshDoc()
    {}
}
<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class BackupController extends AbstractActionController
{

    const BACKUP_FOLDER = "/data/back-up/db";

    protected $doctrineEM;

    /*
     * Defaul Action
     */
    public function indexAction()
    {}

    /**
     * mysql client need to be installed.
     */
    public function dbAction()
    {

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $dbConfig = $nmtPlugin->getDbConfig();

        $user_name = '';
        $pw = '';

        if (isset($dbConfig['username'])) {
            $user_name = $dbConfig['username'];
        }

        if (isset($dbConfig['password'])) {
            $pw = $dbConfig['password'];
        }

        $os = PHP_OS;
        $fileName = ROOT . self::BACKUP_FOLDER . '/sql_' . date("m-d-Y") . '.sql';
        echo $fileName;

        if ($os == \Application\Model\Constants::OS_LINUX) {
            $exe_string = sprintf('mysqldump -u %s --password=%s mla > %s', $user_name, $pw, $fileName);
        } else {
            $exe_string = sprintf('mysqldump -u %s --password=%s mla --result-file "%s"', $user_name, $pw, $fileName);
            // mysqldump -root --password= NMTerfolgkflg#7986 mla --result-file 1.sql
        }

        exec($exe_string);

        sleep(10);

        // AbtractController is EventManagerAware.
        $this->getEventManager()->trigger('system.log', __CLASS__, array(
            'priority' => 7,
            'message' => 'Database backed up manually!'
        ));
    }

    /**
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     *
     * @param EntityManager $doctrineEM
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
    }
}

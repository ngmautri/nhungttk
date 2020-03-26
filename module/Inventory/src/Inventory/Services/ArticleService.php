<?php
namespace Inventory\Services;

use Zend\Permissions\Acl\Acl;
use MLA\Files;
use MLA\Service\AbstractService;
use Zend\Mail\Message;

/**
 *
 * @author nmt
 *        
 */
class ArticleService extends AbstractService
{

    public function initAcl(Acl $acl)
    {
        // TODO
    }

    /**
     *
     * @param unknown $id
     */
    public function createSparepartFolderById($id)
    {
        try {

            $sp_folders_tpl = ROOT . DIRECTORY_SEPARATOR . "module" . DIRECTORY_SEPARATOR . "Inventory" . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . "sparepart_folder";

            // Test
            // $sp_folders_tpl = ROOT . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . "sparepart_folder";

            $sp_dir = ROOT . DIRECTORY_SEPARATOR . "module" . DIRECTORY_SEPARATOR . "Inventory" . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "articles" . DIRECTORY_SEPARATOR . "sparepart_" . $id;

            // Test
            // $sp_dir = ROOT . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "articles" . DIRECTORY_SEPARATOR . "sparepart_" . $id;

            // Setup SMTP transport using PLAIN authentication over TLS
            /*
             * $transport = $this->getServiceLocator()->get('SmtpTransportService');
             * $message = new Message ();
             * $message->addTo ( 'ngmautri@outlook.com' )->addFrom ( 'mib-team@web.de' )->setSubject ( 'Mascot Laos' )->setBody ( "Asset foler " . $asset_dir );
             *
             * $transport->send ( $message );
             */

            if (is_dir($sp_dir)) {
                Files::recursiveCopy($sp_folders_tpl, $sp_dir);
                return $sp_dir;
            } else {
                if (mkdir($sp_dir)) {
                    Files::recursiveCopy($sp_folders_tpl, $sp_dir);
                    return $sp_dir;
                }
            }
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     *
     * @param unknown $id
     * @return NULL
     */
    public function getRootDirById($id)
    {
        try {
            $asset_dir = ROOT . DIRECTORY_SEPARATOR . "module" . DIRECTORY_SEPARATOR . "Inventory" . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "articles" . DIRECTORY_SEPARATOR . "sparepart_" . $id;
            return scandir($asset_dir);
        } catch (Exception $e) {
            return null;
        }
    }

    public function getPicturesPath()
    {
        try {
            $asset_dir = ROOT . DIRECTORY_SEPARATOR . "module" . DIRECTORY_SEPARATOR . "Inventory" . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "articles" . DIRECTORY_SEPARATOR . "pictures";
            return $asset_dir;
        } catch (Exception $e) {
            return null;
        }
    }

    public function getSparepartPath($id)
    {
        try {
            $asset_dir = ROOT . DIRECTORY_SEPARATOR . "module" . DIRECTORY_SEPARATOR . "Inventory" . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "articles" . DIRECTORY_SEPARATOR . "sparepart_" . $id;
            return $asset_dir;
        } catch (Exception $e) {
            return null;
        }
    }
}
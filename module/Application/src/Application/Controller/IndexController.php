<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Doctrine\ORM\EntityManager;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class IndexController extends AbstractActionController
{

    protected $doctrineEM;

    /**
     *
     * {@inheritdoc}
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
    public function indexAction()
    {
        $this->layout("layout/fluid");
        return new ViewModel();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
    public function infoAction()
    {
        $this->layout("layout/user/ajax");

        $model = new ViewModel();
        $model->setTerminal(true);

        return new $model();
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function checkAttachmentAction()
    {
        $criteria = array();

        $list = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAttachment')->findBy($criteria);

        $brokenFile = array();
        if (count($list > 0)) {

            foreach ($list as $pic) {

                /** @var \Application\Entity\NmtApplicationAttachment $pic ;*/
                $pic_folder = getcwd() . $pic->getAttachmentFolder() . $pic->getFileName();
                $pic_folder = str_replace('\\', '/', $pic_folder);
                if (file_exists($pic_folder) == true) {
                    $pic->setFileExits(1);
                } else {
                    $pic->setFileExits(0);
                    $pic->setIsActive(0);
                    $pic->setMarkedForDeletion(1);
                    $brokenFile[] = $pic;
                }

                $this->doctrineEM->persist($pic);
            }

            $this->doctrineEM->flush();
        }

        return new ViewModel(array(
            'brokenFile' => $brokenFile
        ));
    }

    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
        return $this;
    }
}

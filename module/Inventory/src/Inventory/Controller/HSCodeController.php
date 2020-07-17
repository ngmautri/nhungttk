<?php
namespace Inventory\Controller;

use Application\Controller\Contracts\AbstractGenericController;
use Inventory\Application\Service\HSCode\HSCodeTreeService;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class HSCodeController extends AbstractGenericController
{

    protected $hsCodeTreeService;

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function treeAction()
    {
        $builder = $this->getHsCodeTreeService();
        $builder->initCategory();
        $tree = $builder->createComposite(1, 0);

        $viewModel = new ViewModel(array(
            'tree' => $tree->generateJsTree()
        ));
        return $viewModel;
    }

    /**
     *
     * @return \Inventory\Application\Service\HSCode\HSCodeTreeService
     */
    public function getHsCodeTreeService()
    {
        return $this->hsCodeTreeService;
    }

    /**
     *
     * @param HSCodeTreeService $hsCodeTreeService
     */
    public function setHsCodeTreeService(HSCodeTreeService $hsCodeTreeService)
    {
        $this->hsCodeTreeService = $hsCodeTreeService;
    }
}

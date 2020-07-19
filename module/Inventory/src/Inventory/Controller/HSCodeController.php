<?php
namespace Inventory\Controller;

use Application\Controller\Contracts\AbstractGenericController;
use Inventory\Application\Service\HSCode\HSCodeTreeService;
use Inventory\Application\Service\Search\ZendSearch\HSCode\HSCodeSearchQueryImpl;
use Inventory\Application\Service\Search\ZendSearch\HSCode\Filter\HSCodeQueryFilter;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class HSCodeController extends AbstractGenericController
{

    protected $hsCodeTreeService;

    protected $hsCodeQueryService;

    public function searchAction()
    {
        $layout = $this->params()->fromQuery('layout');

        $q = $this->params()->fromQuery('q');
        $q = trim(strip_tags($q));

        $queryFilter = new HSCodeQueryFilter();
        $results = $this->getHsCodeQueryService()->search($q, $queryFilter);

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $viewTemplete = "inventory/hs-code/search-result";

        $viewModel = new ViewModel(array(
            'results' => $results,
            'nmtPlugin' => $nmtPlugin
        ));
        $viewModel->setTemplate($viewTemplete);
        return $viewModel;
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function treeAction()
    {
        try {

            $cat = $this->params()->fromQuery('cat');

            if ($cat == null) {
                $cat = 1;
            }

            $key = "_hs_code_tree_" . $cat;
            $resultCache = $this->getCache()->getItem($key);

            if ($resultCache->isHit()) {

                $cachedTree = $this->getCache()
                    ->getItem($key)
                    ->get();
                $tree = $cachedTree;
            } else {
                $builder = $this->getHsCodeTreeService();
                $builder->initCategory();
                $tree = $builder->createComposite($cat, 0);
                $resultCache->set($tree);
                $this->getCache()->save($resultCache);
                $this->getLogger()->info("HS Code tree cached!");
            }

            $viewModel = new ViewModel(array(
                'tree' => $tree
            ));
            return $viewModel;
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('not_found');
        }
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

    /**
     *
     * @return \Inventory\Application\Service\Search\ZendSearch\HSCode\HSCodeSearchQueryImpl
     */
    public function getHsCodeQueryService()
    {
        return $this->hsCodeQueryService;
    }

    /**
     *
     * @param HSCodeSearchQueryImpl $hsCodeQueryService
     */
    public function setHsCodeQueryService(HSCodeSearchQueryImpl $hsCodeQueryService)
    {
        $this->hsCodeQueryService = $hsCodeQueryService;
    }
}

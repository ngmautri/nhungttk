<?php
namespace Inventory\Controller;

use Application\Controller\Contracts\AbstractGenericController;
use Inventory\Application\Service\MfgCatalog\Tree\MfgCatalogTree;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class MfgCatalogController extends AbstractGenericController
{

    protected $mfgCatalogTree;

    /**
     *
     * @return \Inventory\Application\Service\MfgCatalog\Tree\MfgCatalogTree
     */
    public function getMfgCatalogTree()
    {
        return $this->mfgCatalogTree;
    }

    /**
     *
     * @param MfgCatalogTree $mfgCatalogTree
     */
    public function setMfgCatalogTree(MfgCatalogTree $mfgCatalogTree)
    {
        $this->mfgCatalogTree = $mfgCatalogTree;
    }

    public function searchAction()
    {}

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function treeAction()
    {
        $this->layout("Inventory/layout-blank");
        try {

            $cat = $this->params()->fromQuery('cat');

            if ($cat == null) {
                $cat = 74;
            }

            $key = "_mgf_catalog_tree_" . $cat;
            $resultCache = $this->getCache()->getItem($key);

            if ($resultCache->isHit()) {

                $cachedTree = $this->getCache()
                    ->getItem($key)
                    ->get();
                $tree = $cachedTree;
            } else {
                $builder = $this->getMfgCatalogTree();
                $builder->initTree();
                $tree = $builder->createTree($cat, 0);
                $resultCache->set($tree);
                $this->getCache()->save($resultCache);
                $this->getLogger()->info("Mfg catalog tree cached!");
            }

            $viewModel = new ViewModel(array(
                'tree' => $tree
            ));

            $viewModel->setTemplate("inventory/mfg-catalog/tree1");
            return $viewModel;

            return $viewModel;
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('not_found');
        }
    }
}

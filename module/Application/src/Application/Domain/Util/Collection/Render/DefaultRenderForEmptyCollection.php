<?php
namespace Application\Domain\Util\Collection\Render;

use Application\Application\Helper\Form\FormHelper;
use Application\Domain\Util\Translator;
use Application\Domain\Util\Collection\Contracts\CollectionRenderInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DefaultRenderForEmptyCollection implements CollectionRenderInterface
{

    protected $url;

    protected $toolbar;

    function __construct()
    {
        $f = "<div>%s %s</div>";
        $this->toolbar = sprintf($f, FormHelper::POST_PRE, FormHelper::POST_AFTER);
    }

    /**
     * /**
     *
     * @return mixed
     */
    public function getToolbar()
    {
        return $this->getToolbar();
    }

    public function printAjaxPaginator()
    {}

    /**
     *
     * @param mixed $toolbar
     */
    public function setToolbar($toolbar)
    {
        $this->toolbar = $toolbar;
    }

    public function execute()
    {
        $t = Translator::translate("No thing found!");
        return FormHelper::echoMessage($t);
    }

    public function getUrl()
    {
        return $this->url;
    }

    /**
     *
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function printToolBar()
    {
        return $this->toolbar;
    }
}


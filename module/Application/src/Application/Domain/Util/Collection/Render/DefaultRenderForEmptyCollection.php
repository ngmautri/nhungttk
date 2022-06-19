<?php
namespace Application\Domain\Util\Collection\Render;

use Application\Application\Helper\Form\FormHelper;
use Application\Application\Helper\Form\FormHelperConst;
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

    protected $message;

    function __construct($message = null)
    {
        $f = "<div>%s %s</div>";
        $this->toolbar = sprintf($f, FormHelper::POST_PRE, FormHelper::POST_AFTER);
        $this->message = $message;
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
        $t = $this->message;
        if ($t == null) {
            $t = Translator::translate("No thing found. Please try with other criteria!");
        }
        return FormHelper::echoMessage($t, FormHelperConst::B_LABEL_WARNING);
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


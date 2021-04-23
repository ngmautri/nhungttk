<?php
namespace Application\Form\Contracts;

use Application\Form\Render\DefaultFormRender;
use Application\Form\Render\FormRenderInterface;
use Zend\Form\Form;
use Zend\View\Renderer\PhpRenderer;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class GenericForm extends Form
{

    protected $id;

    protected $redirectUrl;

    protected $formAction;

    abstract protected function addElements();

    public function refresh()
    {
        $this->addElements();
    }

    /**
     *
     * @param FormRenderInterface $render
     * @param PhpRenderer $viewRender
     * @throws \InvalidArgumentException
     * @return string
     */
    public function renderForView(PhpRenderer $viewRender, FormRenderInterface $render = null)
    {
        if ($viewRender == null) {
            throw new \InvalidArgumentException("PhpRenderer is required!");
        }

        if ($render == null) {
            $render = new DefaultFormRender();
        }

        return $render->render($this, $viewRender);
    }

    /**
     *
     * @param FormRenderInterface $render
     * @return string
     */
    public function render(FormRenderInterface $render)
    {
        if ($render == null) {
            $render = new DefaultFormRender();
        }

        return $render->render($this);
    }

    // =========================
    // =========================

    /**
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @return mixed
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    /**
     *
     * @param mixed $redirectUrl
     */
    public function setRedirectUrl($redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;
    }

    /**
     *
     * @return mixed
     */
    public function getFormAction()
    {
        return $this->formAction;
    }

    /**
     *
     * @param mixed $formAction
     */
    public function setFormAction($formAction)
    {
        $this->formAction = $formAction;
    }
}

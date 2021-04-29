<?php
namespace Application\Form\Contracts;

use Application\Form\Render\DefaultFormRender;
use Application\Form\Render\FormRenderInterface;
use Zend\Form\Form;
use Zend\Form\Element\Hidden;
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

    protected $rootId;

    protected $memberId;

    abstract protected function addElements();

    abstract protected function addManualElements();

    public function refresh()
    {
        $this->addElements();
        $this->addManualElements();
    }

    protected function addHiddenElement($name, $value)
    {
        $this->add([
            'type' => Hidden::class,
            'name' => $name,
            'value' => $value
        ]);
    }

    protected function addRootElement()
    {
        $this->add([
            'type' => Hidden::class,
            'name' => 'rootId'
        ]);
    }

    protected function addMemberElement()
    {
        $this->add([
            'type' => Hidden::class,
            'name' => 'memberId'
        ]);
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

    /**
     *
     * @return mixed
     */
    public function getRootId()
    {
        return $this->get("rootId");
    }

    /**
     *
     * @param mixed $rootId
     */
    public function setRootId($rootId)
    {
        $this->rootId = $rootId;
    }

    /**
     *
     * @return mixed
     */
    public function getMemberId()
    {
        return $this->get("memberId");
    }

    /**
     *
     * @param mixed $memberId
     */
    public function setMemberId($memberId)
    {
        $this->memberId = $memberId;
    }
}

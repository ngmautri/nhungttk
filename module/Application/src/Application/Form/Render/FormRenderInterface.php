<?php
namespace Application\Form\Render;

use Application\Form\Contracts\GenericForm;
use Zend\View\Renderer\PhpRenderer;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
interface FormRenderInterface
{

    public function render(GenericForm $form, PhpRenderer $viewRender = null);
}

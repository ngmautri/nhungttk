<?php
namespace Application\Listener;

use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;

class ViewListener implements ListenerAggregateInterface
{

    /**
     *
     * @var array
     */
    protected $listeners = array();

    protected $events;

    /**
     *
     * @param EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(
            MvcEvent::EVENT_FINISH, array($this, 'addTimeStamp'),-100);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Zend\EventManager\ListenerAggregateInterface::detach()
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * Purify HTML output
     */
    public function addTimeStamp(EventInterface $e)
    {
       /*  $body = $e->getResponse()->getContent();
        $html = '<div class="container" style="margin-bottom: 10px;">';
        $html.= '<span class="label label-primary">';
        $html.= round(microtime(true) - TIMESTAMP_START, 5) * 1000 . 'ms';
        $html.= '</span>';
        $html.= '</div>';
        $e->getResponse()->setContent(
            str_replace('</footer>', $html . '</footer>', $body)
        ); */
        
        $body = $e->getResponse()->getContent();
        $html=' <a target="_blank" href="https://github.com/ngmautri/nhungttk" style="font-size:8.5pt; color:gray;">(Github)</a>';
        $html.= '<br><span style="font-size:8.5pt; color:gray;">Page loaded in: ';
        $html.= round(microtime(true) - TIMESTAMP_START, 5) * 1000 . 'ms';
        $html.= '</span>';
        
        $e->getResponse()->setContent(
        str_replace('</footer>', $html . '</footer>', $body)
        );
    }
	
}
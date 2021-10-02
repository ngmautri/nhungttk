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
        $this->listeners[] = $events->attach(MvcEvent::EVENT_FINISH, array(
            $this,
            'addTimeStamp'
        ), - 100);
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
        /*
         * $body = $e->getResponse()->getContent();
         * $html = '<div class="container" style="margin-bottom: 10px;">';
         * $html.= '<span class="label label-primary">';
         * $html.= round(microtime(true) - TIMESTAMP_START, 5) * 1000 . 'ms';
         * $html.= '</span>';
         * $html.= '</div>';
         * $e->getResponse()->setContent(
         * str_replace('</footer>', $html . '</footer>', $body)
         * );
         */
        $t = '<br/>
<ul style="font-size:9pt;
        color:
        gray;
        ">
<li>Source code on %s</li>
<li>Demo videos on %s</li>
<li>Outside of Mascot VPN, please connect to %s</li>
<li>Please connect to %s, if you are on Mascot VPN. It is faster.</li>
</ul >';

        $github = ' <a target="_blank" href="https://github.com/ngmautri/nhungttk" style="font-size:8.5pt; color:black;">Github</a>';
        $vpn = ' <a target="_blank" href="http://10.102.1.107" style="font-size:8.5pt; color:black;">http://10.102.1.107</a>';

        $youtube = '<a target="_blank" href="https://www.youtube.com/channel/UCU3pVFp25_88dwwt71d6gOA" style="font-size:8.5pt; color:black;">Youtube</a>';
        $mla_amp = '<a target="_blank" href="http://mla-app.com" style="font-size:8.5pt; color:black;">mla-app.com</a>';

        $info = sprintf($t, $github, $youtube, $mla_amp, $vpn);

        $t1 = '<span style="font-size:8.5pt; color:gray;">Page loaded in %s</span>';
        $load_time = sprintf($t1, round(microtime(true) - TIMESTAMP_START, 5) * 1000 . 'ms');

        $body = $e->getResponse()->getContent();

        $html = $info;
        $html .= $load_time;

        $e->getResponse()->setContent(str_replace('</footer>', $html . '</footer>', $body));
    }
}
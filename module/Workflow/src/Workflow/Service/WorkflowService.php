<?php
namespace Workflow\Service;

use Workflow\Model\NmtWfNodeTable;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Workflow\DefinitionBuilder;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\Workflow;
use Symfony\Component\Workflow\MarkingStore\SingleStateMarkingStore;
use Symfony\Component\Workflow\Registry;
use Application\Entity\NmtProcurePr;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Workflow\Listener\PrReviewListener;
use Workflow\Listener\WorkflowLogger;

/**
 *
 * @author nmt
 *        
 */
class WorkflowService extends AbstractCategory
{

    protected $doctrineEM;

    private $caseId;

    /**
     */
    public function purchaseRequestWF()
    {
        $definition = new DefinitionBuilder();
        // Transitions are defined with a unique name, an origin place and a destination place
        
        $definition->addPlaces([
            'draft',
            'department_head',
            'department_head_approved',
            'department_head_rejected',
            'fa_budget_check',
            'fa_budget_check_approved',
            'fa_budget_check_rejected',
            'procurement',
            'bought',
            'delivered'
        ])
            ->addTransition(new Transition('submit_to_department_head', 'draft', 'department_head'))
            ->addTransition(new Transition('department_head_yes', 'department_head', 'fa_budget_check'))
            ->addTransition(new Transition('department_head_no', 'department_head', 'department_head_rejected'))
            ->addTransition(new Transition('fa_yes', 'fa_budget_check', 'procurement'))
            ->addTransition(new Transition('fa_no', 'fa_budget_check', 'fa_budget_check_rejected'))
            ->addTransition(new Transition('buy', 'procurement', 'bought'))
            ->addTransition(new Transition('delivery', 'bought', 'delivered'));
        
        $marking = new SingleStateMarkingStore('currentState');
        
        $registry = new Registry();
        
        $dispatcher = new EventDispatcher();
        $l1 = new PrReviewListener($registry);
        $dispatcher->addListener('workflow.PR_WORKFLOW.guard.to_review', array(
            $l1,
            'guardReview'
        ));
        $dispatcher->addListener('workflow.PR_WORKFLOW.entered', array(
            $l1,
            'onSubmitPR'
        ));
        
        $l2 = new WorkflowLogger();
        $dispatcher->addListener('workflow.leave', array(
            $l2,
            'onLeave'
        ));
        
        $workflow = new Workflow($definition->build(), $marking, $dispatcher, "PR_WORKFLOW");
        
        $registry->add($workflow, 'Application\Entity\NmtProcurePr');
        return $registry;
    }

    public function purchase1WF()
    {
        $factory = new \Petrinet\Model\Factory();
        $builder = new \Petrinet\Builder\PetrinetBuilder($factory);
        $petrinet = $builder->connect($builder->place(), $t1 = $builder->transition())
            ->connect($t1, $p2 = $builder->place())
            ->connect($t1, $p3 = $builder->place())
            ->connect($p2, $t2 = $builder->transition())
            ->connect($p3, $t2)
            ->connect($t2, $builder->place())
            ->getPetrinet();
        
        // Instanciates the Dumper
        // $dumper = new \Petrinet\Dumper\GraphvizDumper();
        
        // Dumps the Petrinet structure
        // $string = $dumper->dump($petrinet);
        return ($petrinet);
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \Workflow\Service\AbstractCategory::init()
     */
    public function init()
    {
        $nodes = $this->doctrineEM->getRepository('Application\Entity\NmtWfNode')->findAll();
        
        // $nodes = $this->workFlowNoteTable->fetchAll ();
        
        foreach ($nodes as $row) {
            $id = $row->getNodeId();
            $parent_id = $row->getNodeParentId();
            $this->data[$id] = $row;
            $this->index[$parent_id][] = $id;
        }
        return $this;
    }

    /**
     * Fire transition
     *
     * @param unknown $nodeID
     */
    public function fire($nodeID)
    {
        $node = $this->get($nodeID);
        $wf_node = $node['instance'];
        
        if ($wf_node->getNodeType() == "TRANSITION") {
            
            printf($wf_node->getNodeName() . " is a transition can be fired\n");
            printf(" change Token of input place...\n");
            // Changing Input Place
            $wf_node_parend_id = $wf_node->getNodeParentId();
            $this->changeInputPlace($wf_node_parend_id);
            
            // Changing Input Place
            $this->changeOutPlace($node);
        } else {
            return "can not fire";
        }
    }

    /**
     *
     * @param unknown $id
     */
    private function changeInputPlace($id)
    {
        $node = $this->get($id);
        $wf_node_parent = $node['instance'];
        if ($wf_node_parent->getNodeType() == "PLACE") {
            printf($wf_node_parent->getNodeName() . " is a INPUT PLACE...\n");
        /**
         *
         * @todo create or change token // need caseId; NodeId
         */
            // getToken(case_id, node_id)
            // if not exist create and change.
        } else {
            echo "Not Place";
        }
    }

    /**
     *
     * @param unknown $node:
     *            current transtion
     */
    private function changeOutPlace($node)
    {
        
        // Changing OutPlace
        printf(" change Token of output place...\n");
        $node_children = $node['children'];
        
        if (count($node_children) > 0) {
            foreach ($node_children as $child) {
                $wf_node = $child['instance'];
                if ($wf_node->getNodeType() == "PLACE") {
                    printf($wf_node->getNodeName() . " is a OUTPUT PLACE...\n");
                    // enable transition
                    $node_children_children = $child['children'];
                    if (count($node_children_children > 0)) {
                        foreach ($node_children_children as $t) {
                            $wf_t = $t['instance'];
                            if ($wf_t->getNodeType() == "TRANSTION") {
                                printf($wf_t->getNodeName() . ' is enabled\n');
                            }
                        }
                    }
                } else {
                    echo "something wrong";
                }
            }
        }
    }

    /**
     *
     * @param unknown $node:
     *            input place
     */
    private function enableTransition($node)
    {}

    // =====================================
    public function getCase()
    {
        return $this->case;
    }

    public function setCase($case)
    {
        $this->case = $case;
        return $this;
    }

    public function getCaseId()
    {
        return $this->caseId;
    }

    public function setCaseId($caseId)
    {
        $this->caseId = $caseId;
        return $this;
    }

    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
        return $this;
    }
}
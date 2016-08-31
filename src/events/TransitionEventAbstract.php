<?php


namespace ValidatedStatemachine\events;

use Symfony\Component\EventDispatcher\Event;
use ValidatedStatemachine\models\Transition;
use ValidatedStatemachine\StateMachine;

class TransitionEventAbstract extends Event
{
    /**
     * @var StateMachine
     */
    protected $stateMachine;
    /**
     * @var Transition
     */
    protected $transition;

    /**
     * TransitionExecutedEvent constructor.
     *
     * @param StateMachine $stateMachine
     */
    public function __construct($stateMachine, Transition $transition)
    {
        $this->stateMachine = $stateMachine;
        $this->transition   = $transition;
    }

    /**
     * Get state machine
     *
     * @return StateMachine
     */
    public function getStateMachine()
    {
        return $this->stateMachine;
    }

    /**
     * Return transition which was executed
     *
     * @return Transition
     */
    public function getTransition()
    {
        return $this->transition;
    }
}
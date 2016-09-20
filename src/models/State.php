<?php

namespace ValidatedStatemachine\models;

use ValidatedStatemachine\exceptions\StateDefinitionIncorrectException;

/**
 * Class State
 *
 * State class of StateMachine pattern
 *
 * @package app\modules\statemachine\models
 */
class State
{
    /**
     * @var integer
     */
    public $stateId;

    /**
     * @var array
     */
    protected $payload;

    /**
     * @var array
     */
    protected $transitions = [];

    /**
     * Set state params. All additional info on state will be available in payload
     *
     * @param mixed $stateId ID of the state
     * @param array $payload Additional info about the state which will be accessible from State object
     */
    public function __construct($stateId, $payload = [])
    {
        $this->stateId = $stateId;
        $this->payload = $payload;
    }

    /**
     * Get available transitions for this State
     *
     * @return Transition[]
     */
    public function getTransitions()
    {
        return $this->transitions;
    }

    /**
     * Add new transition to State
     *
     * @param Transition $transition Transition to add
     *
     * @return $this
     * @throws StateDefinitionIncorrectException
     */
    public function addTransition(Transition $transition)
    {
        if (!in_array($this, $transition->from)) {
            throw new StateDefinitionIncorrectException("State $this->stateId cannot apply transition $transition->name");
        }

        $this->transitions[$transition->name] = $transition;

        return $this;
    }

    /**
     * Add group of transitions
     *
     * @param array $transitions Array which consists of Transition objects
     *
     * @return $this
     */
    public function addTransitions(array $transitions)
    {
        foreach ($transitions as $transition) {
            $this->addTransition($transition);
        }

        return $this;
    }

    /**
     * Is it possible to apply this transition to this State?
     *
     * @param Transition $candidateTransition Transition we are checking
     *
     * @return bool
     */
    public function can(Transition $candidateTransition)
    {
        $availableTransitions = $this->getTransitions();

        foreach ($availableTransitions as $transition) {
            if ($transition->name == $candidateTransition->name) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return string representation of the state
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->stateId;
    }

    /**
     * Get payload of the transition
     *
     * @return array
     */
    public function getPayload()
    {
        return $this->payload;
    }
}

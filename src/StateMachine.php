<?php

namespace ValidatedStatemachine;

use ValidatedStatemachine\events\EventDispatcherSingleton;
use ValidatedStatemachine\events\TransitionExecutedEvent;
use ValidatedStatemachine\events\TransitionFailedEvent;
use ValidatedStatemachine\models\State;
use ValidatedStatemachine\models\Transition;

/**
 * Class StateMachine
 *
 * Trait which gives StateMachine behaviour to model
 *
 * @author  Denys Dorofeiev <denys.dorofeiev@westwing.de>
 * @package app\modules\statemachine
 */
trait StateMachine
{
    public $error = "";

    protected static $CACHE = [];

    /**
     * Set state to the State Machine
     *
     * @param State $state State which will be applied to the model
     *
     * @return $this
     */
    public function setState(State $state)
    {
        $this->{$this->getStateProperty()} = $state->stateId;

        return $this;
    }


    /**
     * Retrieve current state of the State Machine
     *
     * @return State
     */
    public function getState()
    {
        $stateId = $this->{$this->getStateProperty()};

        return $this->initState($stateId);
    }

    /**
     * Retrieve all available transitions for current State of model
     *
     * @return Transition[]
     */
    public function getAvailableTransitions()
    {
        $availableTransitionsUnvalidated = $this->getState()->getTransitions();

        $availableTransitionsValidated = [];
        foreach ($availableTransitionsUnvalidated as $transition) {
            if ($transition->validate($this)) {
                $availableTransitionsValidated[] = $transition;
            }
        }

        return $availableTransitionsValidated;
    }


    /**
     * Can execute transition
     *
     * Checks whether a given Transition can be executed as it is available in current state and it is validated
     *
     * @param Transition|string $transition Transition being applied to state
     *
     * @return bool
     * @throws exceptions\StateDefinitionIncorrectException
     */
    public function canExecuteTransition(Transition $transition)
    {
        $this->error = "";

        if (!$transition->validate($this, $this->error)) {
            return false;
        }

        return $this->getState()->can($transition);
    }

    /**
     * Execute transition
     *
     * Transition is executed if it is available in current state and it is validated
     *
     * @param Transition|string $transition Transition being applied to state
     * @param mixed             $data       Additional date to be passed to transition
     *
     * @return bool
     * @throws \Exception
     */
    public function executeTransition(Transition $transition, $data = [])
    {
        $transition->setPayload($data);

        if (!$this->canExecuteTransition($transition)) {
            $event = new TransitionFailedEvent($this, $transition);
            EventDispatcherSingleton::getDispatcher()->dispatch(TransitionExecutedEvent::EVENT_NAME, $event);;
            return false;
        }

        $this->setState($transition->to);
        $event = new TransitionExecutedEvent($this, $transition);
        EventDispatcherSingleton::getDispatcher()->dispatch(TransitionExecutedEvent::EVENT_NAME, $event);

        return true;
    }

    /**
     * Create Transition object by it's identifier
     *
     * @param string $transitionName Name of Transition from StateMachine specification
     *
     * @return Transition|bool
     */
    public function getTransitionByName($transitionName)
    {
        $transitions = $this->getAvailableTransitions();

        foreach ($transitions as $transition) {
            if ($transition->name == $transitionName) {
                return $transition;
            }
        }

        return false;
    }

    /**
     * Returns first validator which returned false during last transition attempt
     *
     * @return string
     */
    public function getValidationError()
    {
        return $this->error;
    }

    /**
     * Initialize state from its definition
     *
     * @author Denys Dorofeiev <denys.dorofeiev@westwing.de>
     *
     * @param integer $stateId ID of state
     *
     * @return State
     */
    protected function initState($stateId)
    {
        $cacheKey = get_class($this) . '-' . $stateId;
        if (empty(self::$CACHE[$cacheKey])) {
            $state                  = $this->getSpecification()->getState($stateId);
            self::$CACHE[$cacheKey] = $state;
        } else {
            $state = self::$CACHE[$cacheKey];
        }

        return $state;
    }

    /**
     * Returns the specification of the Statemachine
     *
     * @author Denys Dorofeiev <denys.dorofeiev@westwing.de>
     *
     * @return StateMachineSpecification
     */
    abstract protected function getSpecification();

    /**
     * Returns property name of the statemachine which will be used to store its state
     *
     * @author Denys Dorofeiev <denys.dorofeiev@westwing.de>
     *
     * @return string
     */
    abstract protected function getStateProperty();

    /**
     * Clear cache
     *
     * @author Krzysztof Suchanek <krzysztof.suchanek@westwing.de>
     */
    public static function clearCache()
    {
        self::$CACHE = [];
    }
}

<?php

namespace ValidatedStatemachine;

use ValidatedStatemachine\exceptions\StateDefinitionIncorrectException;
use ValidatedStatemachine\exceptions\TransitionNotFoundException;
use ValidatedStatemachine\models\State;
use ValidatedStatemachine\models\Transition;

/**
 * Class StateMachineSpecification
 *
 * It is an abstract class which should be extended for every StateMachine specification
 *
 * @package app\modules\statemachine
 */
abstract class StateMachineSpecification
{
    protected static $CACHE;

    /**
     * Initializes the State object from State ID from database
     *
     * @param integer $state ID of the state
     *
     * @return State
     * @throws StateDefinitionIncorrectException
     */
    public function getState($stateId)
    {
        $cacheKey = get_class($this) . '-' . $stateId;
        if (empty(self::$CACHE[$cacheKey])) {
            $states = $this->getStateDefinitions();
            if (!isset($states[$stateId])) {
                throw new StateDefinitionIncorrectException('State with stateId "' . $stateId . '" not found in ' . get_class($this));
            }

            $state = new State($stateId, $states[$stateId]);

            self::$CACHE[$cacheKey] = $state;
        } else {
            $state = self::$CACHE[$cacheKey];
        }

        $this->initTransitionsForState($state);

        return $state;
    }

    /**
     * Initializes Transitions for given State object
     *
     * @param State $state State to add transitions
     *
     * @return Transition[]
     */
    protected function initTransitionsForState(State $state)
    {
        $transitions = $this->getTransitionDefinitions();
        $states      = $this->getStateDefinitions();

        $resultTransitionsForState = [];

        foreach ($transitions as $transitionId => $transition) {
            if ($transition instanceof Transition) {
                if (in_array($state, $transition->from)) {
                    $resultTransitionsForState[$transition->name] = $transition;
                }

                continue;
            }

            $from = $transition['from'];
            if (!is_array($from)) {
                $from = [$from];
            }
            if (in_array($state->stateId, $from)) {
                $toStateId = $transition['to'];

                $stateFrom                                = $state;
                $stateTo                                  = new State($toStateId, $states[$toStateId]);
                $transitionObject                         = new Transition($transitionId, [$stateFrom], $stateTo, $transition);
                $resultTransitionsForState[$transitionId] = $transitionObject;
            }
        }
        $state->addTransitions($resultTransitionsForState);

        return $resultTransitionsForState;
    }

    /**
     * Gets state definitions for current specification
     *
     * @return array
     */
    abstract public function getStateDefinitions();

    /**
     * Gets transition definitions for current specification
     *
     * @return array
     */
    abstract public function getTransitionDefinitions();
}

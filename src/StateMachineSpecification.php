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
 * @author  Denys Dorofeiev <denys.dorofeiev@westwing.de>
 * @package app\modules\statemachine
 */
abstract class StateMachineSpecification
{
    /**
     * Initializes the State object from State ID from database
     *
     * @author Denys Dorofeiev <denys.dorofeiev@westwing.de>
     *
     * @param integer|State $state ID of the state
     *
     * @return State
     * @throws StateDefinitionIncorrectException
     */
    public function getState($state)
    {
        if (!$state instanceof State) {
            $states = $this->getStateDefinitions();
            if (!isset($states[$state])) {
                throw new StateDefinitionIncorrectException('State with stateId "' . $state . '" not found in ' . get_class($this));
            }

            $state = new State($state, $states[$state]);
        }

        $this->initTransitionsForState($state);

        return $state;
    }

    /**
     * Initializes Transitions for given State object
     *
     * @author Denys Dorofeiev <denys.dorofeiev@westwing.de>
     *
     * @param State $state State to add transitions
     *
     * @return array
     */
    public function initTransitionsForState(State $state)
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
                $stateTo                                  = $this->stateFactory($toStateId, $states[$toStateId]);
                $transitionObject                         = new Transition($transitionId, [$stateFrom], $stateTo, $transition);
                $resultTransitionsForState[$transitionId] = $transitionObject;
            }
        }
        $state->addTransitions($resultTransitionsForState);

        return $resultTransitionsForState;
    }

    /**
     * Method for creation of State objects
     *
     * @author Denys Dorofeiev <denys.dorofeiev@westwing.de>
     *
     * @param integer $stateId ID of the state
     * @param array   $payload Additional info about the state which will be accessible from State object
     *
     * @return State
     */
    public function stateFactory($stateId, $payload)
    {
        return new State($stateId, $payload);
    }

    /**
     * Gets state definitions for current specification
     *
     * @author Denys Dorofeiev <denys.dorofeiev@westwing.de>
     *
     * @return array
     */
    abstract public function getStateDefinitions();

    /**
     * Gets transition definitions for current specification
     *
     * @author Denys Dorofeiev <denys.dorofeiev@westwing.de>
     *
     * @return array
     */
    abstract public function getTransitionDefinitions();
}

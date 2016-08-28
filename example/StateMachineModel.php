<?php

namespace ValidatedStatemachine\example;

use ValidatedStatemachine\StateMachine;
use ValidatedStatemachine\StateMachineSpecification;

class StateMachineModel
{
    use StateMachine;

    public $stateId;

    /**
     * Returns the specification of the Statemachine
     *
     * @author Denys Dorofeiev <denys.dorofeiev@westwing.de>
     *
     * @return StateMachineSpecification
     */
    protected function getSpecification()
    {
        return new ExampleStateMachineSpecification();
    }

    /**
     * Returns property name of the statemachine which will be used to store its state
     *
     * @author Denys Dorofeiev <denys.dorofeiev@westwing.de>
     *
     * @return string
     */
    protected function getStateProperty()
    {
        return 'stateId';
    }
}
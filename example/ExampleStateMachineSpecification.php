<?php

namespace ValidatedStatemachine\example;

use ValidatedStatemachine\models\Transition;
use ValidatedStatemachine\StateMachineSpecification;

class ExampleStateMachineSpecification extends StateMachineSpecification
{
    const STATE_1 = 100;
    const STATE_2 = 200;
    const STATE_3 = 300;
    const STATE_4 = 400;

    const TRANSITION_FROM1_TO_2 = "from_1_to_2";
    const TRANSITION_FROM3_TO_4 = "from_3_to_4";

    public function getStateDefinitions()
    {
        return [
            self::STATE_1 => [],
            self::STATE_2 => [],
            self::STATE_3 => [],
            self::STATE_4 => [],
        ];
    }

    public function getTransitionDefinitions()
    {
        return [
            self::TRANSITION_FROM1_TO_2 => [
                'from' => self::STATE_1,
                'to'   => self::STATE_2
            ],
            self::TRANSITION_FROM3_TO_4 => [
                'from' => self::STATE_3,
                'to'   => self::STATE_4,
                'validators' => [
                    [new Validator(), []]
                ]
            ],
        ];
    }
}

class Validator implements \ValidatedStatemachine\models\Validator
{
    /**
     * Transition validators should implement method Validate which accepts transition which is being validated,
     * model on which the transition is being applied
     *
     * @param Transition $transition Transition which is being validated
     * @param \ValidatedStatemachine\StateMachine      $model      Model on which the transition is being applied
     *
     * @return mixed
     */
    public function validate(Transition $transition, $model)
    {
        return !empty($model->shouldExecuteTransition) && $model->shouldExecuteTransition === true;
    }
}

<?php

namespace ValidatedStatemachine\example;

use ValidatedStatemachine\models\Transition;
use ValidatedStatemachine\StateMachineSpecification;

class ExampleStateMachineSpecification extends StateMachineSpecification
{
    const STATE_PENDING = "pending";
    const STATE_SHIPPED = "shipped";
    const STATE_CANCELLED = "cancelled";
    const STATE_DELIVERED = "delivered";
    const STATE_FAILED = "failed";

    const TRANSITION_SHIP = "ship";
    const TRANSITION_CANCEL = "cancel";
    const TRANSITION_DELIVER = "deliver";
    const TRANSITION_DELIVERY_FAILED = "delivery_failed";

    public function getStateDefinitions()
    {
        return [
            self::STATE_PENDING => ["color" => "orange"],
            self::STATE_SHIPPED => ["color" => "green"],
            self::STATE_CANCELLED => ["color" => "darkgrey"],
            self::STATE_DELIVERED => ["color" => "darkgreen"],
            self::STATE_FAILED => ["color" => "red"],
        ];
    }

    public function getTransitionDefinitions()
    {
        return [
            self::TRANSITION_SHIP => [
                'from' => self::STATE_PENDING,
                'to'   => self::STATE_SHIPPED,
                'validators' => [
                    [new Validator(), []]
                ]
            ],
            self::TRANSITION_CANCEL => [
                'from' => self::STATE_PENDING,
                'to'   => self::STATE_CANCELLED
            ],
            self::TRANSITION_DELIVER => [
                'from' => self::STATE_SHIPPED,
                'to'   => self::STATE_DELIVERED
            ],
            self::TRANSITION_DELIVERY_FAILED => [
                'from' => self::STATE_SHIPPED,
                'to'   => self::STATE_FAILED
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

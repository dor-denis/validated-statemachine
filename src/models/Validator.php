<?php

namespace ValidatedStatemachine\models;
use ValidatedStatemachine\StateMachine;

/**
 * Interface Validator
 *
 * This is interface for Transition validators
 *
 * @package app\modules\statemachine\models
 */
interface Validator
{
    /**
     * Transition validators should implement method Validate which accepts transition which is being validated,
     * model on which the transition is being applied
     *
     * @param Transition   $transition Transition which is being validated
     * @param StateMachine $model      Model on which the transition is being applied
     *
     * @return mixed
     */
    public function validate(Transition $transition, $model);
}

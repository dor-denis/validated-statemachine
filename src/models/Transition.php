<?php

namespace ValidatedStatemachine\models;

use ValidatedStatemachine\exceptions\StateDefinitionIncorrectException;
use ValidatedStatemachine\StateMachine;

/**
 * Class Transition
 */
class Transition
{
    /**
     * @var string
     */
    public $name = '';
    /**
     * @var array
     */
    public $settings = [];

    /**
     * @var State
     */
    public $to;

    /**
     * @var State[]
     */
    public $from;

    /**
     * @var mixed additional data passed to transition
     */
    protected $payload;

    /**
     * Sets initial parameters of Transition
     *
     * @param string  $name     ID of Transition
     * @param State[] $from     State for this Transition is applicable
     * @param State   $to       State this Transition will transfer the model to
     * @param array   $settings Additional data for Transition
     */
    public function __construct(
        $name,
        array $from,
        State $to,
        $settings = []
    ) {
        $this->name     = $name;
        $this->to       = $to;
        $this->from     = $from;
        $this->settings = $settings;
    }

    /**
     * Getter for additional data
     *
     * @return mixed
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * Setter for additional data
     *
     * @param mixed $payload Additional data to be passed to transition
     */
    public function setPayload($payload)
    {
        $this->payload = $payload;
    }

    /**
     * Check if this transition passes the validations. Return true if no validators were specified
     *
     * @param StateMachine $model StateMachine to validate
     *
     * @return bool
     * @throws StateDefinitionIncorrectException
     */
    public function validate($model)
    {
        if (!isset($this->settings['validators'])) {
            return true;
        }

        foreach ($this->settings['validators'] as $validatorDefinition) {
            if (!($validatorDefinition instanceof Validator) && count($validatorDefinition) != 2) {
                throw new StateDefinitionIncorrectException('Validator specification should have also params sub array');
            }

            $validator = $this->validatorFactory($validatorDefinition);
            if (!$validator->validate($this, $model)) {
                $model->error = get_class($validator);
                return false;
            }
        }

        return true;
    }

    /**
     * Create new validator from array
     *
     * @param array|Validator $validatorDefinition validatorClassArray
     *
     * @return Validator
     */
    protected function validatorFactory($validatorDefinition)
    {
        if ($validatorDefinition instanceof Validator) {
            return $validatorDefinition;
        }

        list($validatorClass, $params) = $validatorDefinition;

        return new $validatorClass($params);
    }

    /**
     * String version of the transition
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}

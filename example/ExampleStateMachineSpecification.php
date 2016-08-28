<?php

namespace ValidatedStatemachine\example;

use ValidatedStatemachine\StateMachineSpecification;

class ExampleStateMachineSpecification extends StateMachineSpecification
{
    const STATE_1 = 100;
    const STATE_2 = 200;
    const STATE_3 = 300;
    const STATE_4 = 400;

    const TRANSITION_FROM1_TO_2 = "from_1_to_2";
    const TRANSITION_FROM3_TO_4 = "from_3_to_4";

    /**
     * Gets state definitions for current specification
     *
     * @author Denys Dorofeiev <denys.dorofeiev@westwing.de>
     *
     * @return array
     */
    public function getStateDefinitions()
    {
        return [
            self::STATE_1 => [],
            self::STATE_2 => [],
            self::STATE_3 => [],
            self::STATE_4 => [],
        ];
    }

    /**
     * Gets transition definitions for current specification
     *
     * @author Denys Dorofeiev <denys.dorofeiev@westwing.de>
     *
     * @return array
     */
    public function getTransitionDefinitions()
    {
        return [
            self::TRANSITION_FROM1_TO_2 => [
                'from' => self::STATE_1,
                'to'   => self::STATE_2
            ],
            self::TRANSITION_FROM3_TO_4 => [
                'from' => self::STATE_3,
                'to'   => self::STATE_4
            ],
        ];
    }
}

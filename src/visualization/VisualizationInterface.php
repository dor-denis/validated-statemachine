<?php

namespace ValidatedStatemachine\visualization;

use ValidatedStatemachine\StateMachineSpecification;

interface VisualizationInterface
{
    /**
     * Visualize state machine and return result file as string
     *
     * @param StateMachineSpecification $stateMachineSpecification
     *
     * @return string
     */
    public function visualize(StateMachineSpecification $stateMachineSpecification);
}

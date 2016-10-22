<?php

namespace ValidatedStatemachine\visualization\html;

use ValidatedStatemachine\StateMachineSpecification;
use ValidatedStatemachine\visualization\VisualizationInterface;

class HtmlVisualization implements VisualizationInterface
{
    /**
     * Visualize state machine and return result file as string
     *
     * @param StateMachineSpecification $stateMachineSpecification
     *
     * @return string
     */
    public function visualize(StateMachineSpecification $stateMachineSpecification)
    {
        $json = json_encode($this->toArray($stateMachineSpecification));

        $template = file_get_contents(__DIR__ . "/data/template.html");

        $js =
            file_get_contents(__DIR__ . "/data/js/sigma.min.js") . "\n" .
            file_get_contents(__DIR__ . "/data/js/sigma.layout.forceAtlas2.min.js") . "\n" .
            file_get_contents(__DIR__ . "/data/js/sigma.plugins.dragNodes.min.js") . "\n" .
            file_get_contents(__DIR__ . "/data/js/sigma.renderers.edgeLabels.min.js") . "\n" .
            file_get_contents(__DIR__ . "/data/js/sigma.renderers.parallelEdges.min.js");;

        $result = str_replace('$JSON$', $json, $template);

        return str_replace('$JS$', $js, $result);
    }

    /**
     * Converts the state machine to array
     *
     * @param StateMachineSpecification $stateMachineSpecification
     *
     * @return array
     */
    protected function toArray(StateMachineSpecification $stateMachineSpecification)
    {
        $states = array_keys($stateMachineSpecification->getStateDefinitions());

        $nodes = [];
        $edges = [];

        foreach ($states as $stateId) {
            $state   = $stateMachineSpecification->getState($stateId);
            $nodes[] = [
                "id"    => $state->stateId,
                "label" => isset($state->getPayload()['label']) ? $state->getPayload()['label'] : "$state->stateId",
                "x"     => mt_rand() / mt_getrandmax(),
                "y"     => mt_rand() / mt_getrandmax(),
                "size"  => 1,
                "color" => isset($state->getPayload()['color']) ? $state->getPayload()['color'] : "#f00",
            ];

            $transitions = $state->getTransitions();
            foreach ($transitions as $transition) {
                foreach ($transition->from as $fromState) {
                    $parallelEdgeKey = $fromState->stateId . $transition->to->stateId;
                    $count           = 0;
                    if (isset($edges[$parallelEdgeKey])) {
                        $count = $edges[$parallelEdgeKey]['count'] + 1;
                    }

                    $edges[$parallelEdgeKey] = [
                        "id"     => $transition->name . $fromState->stateId . $transition->to->stateId,
                        "label"  => isset($transition->getPayload()['label']) ? $transition->getPayload()['label'] : $transition->name,
                        "source" => $fromState->stateId,
                        "target" => $transition->to->stateId,
                        "size"   => 2,
                        "color"  => "#ccc",
                        "count"  => $count,
                        "type"   => "curvedArrow"
                    ];
                }
            }
        }

        return [
            "nodes" => $nodes,
            "edges" => array_values($edges)
        ];
    }
}

<?php

namespace tests;

use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use ValidatedStatemachine\exceptions\StateDefinitionIncorrectException;
use ValidatedStatemachine\models\State;
use ValidatedStatemachine\models\Transition;
use ValidatedStatemachine\models\Validator;
use ValidatedStatemachine\StateMachineSpecification;

class StateMachineSpecificationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var StateMachineSpecification|PHPUnit_Framework_MockObject_MockObject
     */
    protected $stateMachineSpecification;

    public function setUp()
    {
        $this->stateMachineSpecification = $this->getMockBuilder(StateMachineSpecification::class)->getMockForAbstractClass();
    }

    /**
     * @param array $stateDefinitions
     * @param array $transitionDefinitions
     *
     * @dataProvider stateDefinitionsProvider
     */
    public function testGetState($stateDefinitions, $transitionDefinitions)
    {
        $this->stateMachineSpecification->expects($this->any())->method('getStateDefinitions')->willReturn($stateDefinitions);
        $this->stateMachineSpecification->expects($this->any())->method('getTransitionDefinitions')->willReturn($transitionDefinitions);

        foreach ($stateDefinitions as $stateId => $stateObject) {
            /**
             * @var State $state
             */
            $state = $stateObject instanceof State ? $stateObject : $stateId;
            $state = $this->stateMachineSpecification->getState($state);
            $this->assertEquals($stateObject instanceof State ? $stateObject->stateId : $stateId, $state->stateId);
        }
    }

    /**
     * @param $stateDefinitions
     * @param $transitionDefinitions
     *
     * @dataProvider stateDefinitionsProvider
     */
    public function testGetInvalidStateThrowsException($stateDefinitions, $transitionDefinitions)
    {
        $this->stateMachineSpecification->expects($this->any())->method('getStateDefinitions')->willReturn($stateDefinitions);
        $this->stateMachineSpecification->expects($this->any())->method('getTransitionDefinitions')->willReturn($transitionDefinitions);

        $this->expectException(StateDefinitionIncorrectException::class);
        $this->stateMachineSpecification->getState(999);
    }

    public function stateDefinitionsProvider()
    {
        $stateObject1 = new State(100, ["Test Payload Key" => "Test Payload value"]);
        $stateObject2 = new State(200, ["Test Payload Key" => "Test Payload value"]);
        $stateObject3 = new State(300, ["Test Payload Key" => "Test Payload value"]);

        return [
            "Sample definition"       => [
                [
                    100 => [
                        "Test Payload Key" => "Test Payload value"
                    ],
                    200 => [
                        "Test Payload Key" => "Test Payload value"
                    ],
                    300 => [
                        "Test Payload Key" => "Test Payload value"
                    ],
                ],
                [
                    '100_200' => [
                        'from'       => 100,
                        'to'         => 200,
                        'validators' => []
                    ],
                    '200_300' => [
                        'from'       => 200,
                        'to'         => 300,
                        'validators' => []
                    ],
                ]
            ],
            "Definition with objects" => [
                [
                    $stateObject1,
                    $stateObject2,
                    $stateObject3,
                ],
                [
                    new Transition("100_200", [$stateObject1], $stateObject2),
                    new Transition("200_300", [$stateObject2], $stateObject3),
                ]
            ]
        ];
    }
}

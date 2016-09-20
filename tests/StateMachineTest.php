<?php
namespace tests;

use Faker\Factory;
use Faker\Generator;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use ValidatedStatemachine\models\State;
use ValidatedStatemachine\models\Transition;
use ValidatedStatemachine\StateMachine;
use ValidatedStatemachine\StateMachineSpecification;

/**
 * Class StateMachineTest
 *
 * @author Denys Dorofeiev <denys.dorofeiev@westwing.de>
 */
class StateMachineTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Generator
     */
    protected $faker;
    /**
     * @var StateMachine|PHPUnit_Framework_MockObject_MockObject
     */
    protected $statemachine;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Transition
     */
    protected $transitionMock;
    /**
     * @var string
     */
    protected $statePropertyName;

    /**
     * @var StateMachineSpecification|PHPUnit_Framework_MockObject_MockObject
     */
    protected $stateMachineSpecification;

    protected function setUp()
    {
        $this->faker                 = Factory::create();
        $this->statemachine          = $this->getMockBuilder(StateMachine::class)->enableProxyingToOriginalMethods()->getMockForTrait();
        $this->statemachine->carrier = $this->faker->word;
        $this->statePropertyName     = $this->faker->word;
        $this->statemachine->expects($this->any())->method('getStateProperty')->willReturn($this->statePropertyName);
        $this->stateMachineSpecification = $this->getMockBuilder(StateMachineSpecification::class)->getMock();
        $this->statemachine->expects($this->any())->method('getSpecification')->willReturn($this->stateMachineSpecification);
    }

    public function testSetState()
    {
        $state = new State($this->faker->randomNumber(), []);

        $stateMachine = $this->statemachine->setState($state);

        $this->assertEquals($state->stateId, $this->statemachine->{$this->statePropertyName});
        $this->assertContains("Trait_StateMachine", get_class($stateMachine));
    }

    public function testGetState()
    {
        $stateId                                        = $this->faker->randomNumber();
        $state                                          = new State($stateId, []);
        $this->statemachine->{$this->statePropertyName} = $stateId;
        $this->stateMachineSpecification->expects($this->once())->method('getState')->with($stateId)->willReturn($state);

        $this->assertEquals($state->stateId, $this->statemachine->getState()->stateId);
    }

    /**
     * getAvailableTransitions method should filter out the unvalidated transitions
     */
    public function testGetAvailableTransitions()
    {
        $notValidatedTransition = $this->getMockBuilder(Transition::class)->disableOriginalConstructor()->getMock();
        $notValidatedTransition->expects($this->any())->method('validate')->willReturn(false);
        $notValidatedTransition->expects($this->any())->method('__toString')->willReturn("notValidated");
        $notValidatedTransition->name = 'notValidated';

        $validatedTransition = $this->getMockBuilder(Transition::class)->disableOriginalConstructor()->getMock();
        $validatedTransition->expects($this->any())->method('validate')->willReturn(true);
        $validatedTransition->expects($this->any())->method('__toString')->willReturn("validated");
        $validatedTransition->name = 'validated';

        $stateId = $this->faker->randomNumber();
        $state   = $this->getMockBuilder(State::class)->disableOriginalConstructor()->getMock();

        $state->expects($this->exactly(3))->method('getTransitions')->willReturn(
            [
                $validatedTransition,
                $notValidatedTransition,
                $validatedTransition,
                $notValidatedTransition
            ]
        );
        $this->statemachine->{$this->statePropertyName} = $stateId;
        $this->stateMachineSpecification->expects($this->any())->method('getState')->with($stateId)->willReturn($state);

        $availableTransitions = $this->statemachine->getAvailableTransitions();
        $this->assertCount(2, $availableTransitions, "All validated transitions should be returned");

        foreach ($availableTransitions as $availableTransition) {
            $this->assertEquals('validated', (string)$availableTransition);
        }

        $this->assertEquals($validatedTransition, $this->statemachine->getTransitionByName('validated'));
        $this->assertNull($this->statemachine->getTransitionByName('nonExisting'));
    }

    /**
     * @param Transition|string|PHPUnit_Framework_MockObject_MockObject $transition
     * @param                                                           $validates
     * @param                                                           $can
     * @param                                                           $result
     * @dataProvider canExecuteTransitionProvider
     */
    public function testCanExecuteTransition(Transition $transition, $validates, $can, $result)
    {
        $transition->expects($this->any())->method('validate')->willReturn($validates);

        $stateId   = $this->faker->randomNumber();
        $stateMock = $this->getMockBuilder(State::class)->disableOriginalConstructor()->getMock();
        $stateMock->expects($this->any())->method('can')->willReturn($can);

        $this->statemachine->{$this->statePropertyName} = $stateId;
        $this->stateMachineSpecification->expects($this->any())->method('getState')->with($stateId)->willReturn($stateMock);

        $this->assertEquals($result, $this->statemachine->canExecuteTransition($transition));

        $transition->expects($this->any())->method('setPayload');

        $executed = $this->statemachine->executeTransition($transition);
        $this->assertEquals($result, $executed);
    }

    public function canExecuteTransitionProvider()
    {
        /**
         * @var Transition|PHPUnit_Framework_MockObject_MockObject $transition
         */
        $transition     = $this->getMockBuilder(Transition::class)->disableOriginalConstructor()->getMock();
        $transition->to = new State(mt_rand(0, 10000), []);

        return [
            'Can execute'                                             => [$transition, true, true, true],
            'Transition is not validated'                             => [$transition, false, true, false],
            'This transition is not available from the current state' => [$transition, true, false, false],
        ];
    }

    public function testCache()
    {
        $stateId                                        = $this->faker->randomNumber();
        $state                                          = new State($stateId, []);
        $this->statemachine->{$this->statePropertyName} = $stateId;
        $this->stateMachineSpecification->expects($this->exactly(3))->method('getState')->with($stateId)->willReturn($state);

        $this->statemachine->getState();
        $this->statemachine->getState();

        $this->statemachine->clearCache();
        $this->statemachine->getState();
    }
}

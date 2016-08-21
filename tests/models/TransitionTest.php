<?php


namespace tests\models;

use Faker\Factory;
use Faker\Generator;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use ValidatedStatemachine\models\State;
use ValidatedStatemachine\models\Transition;
use ValidatedStatemachine\models\Validator;
use ValidatedStatemachine\StateMachine;

class TransitionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Transition
     */
    protected $transition;

    public function setUp()
    {
        /**
         * @var Generator $faker
         */
        $faker            = Factory::create();
        $this->transition = new Transition($faker->word, [new State(100)], new State(200));
    }

    public function testSetGetAdditionalData()
    {
        $payload = ['validators' => 'test', 'otherTest' => 'test1'];
        $this->transition->setPayload($payload);

        $this->assertEquals($payload, $this->transition->getPayload());
    }

    public function testValidatesWithoutValidators()
    {
        $stateMachine = $this->getMockBuilder(StateMachine::class)->getMockForTrait();
        $this->assertTrue($this->transition->validate($stateMachine));
    }

    public function testFailsIfOneValidatorFails()
    {
        /**
         * @var Transition|PHPUnit_Framework_MockObject_MockObject   $transition
         * @var StateMachine|PHPUnit_Framework_MockObject_MockObject $stateMachine
         */
        $transition   = $this->getMockBuilder(Transition::class)
            ->setMethods(['validatorFactory'])
            ->disableOriginalConstructor()
            ->getMock();
        $stateMachine = $this->getMockBuilder(StateMachine::class)->getMockForTrait();

        $transition->settings = [
            'validators' => [
                ['Path/To/Validator', ['validatorParam1', 'validatorParam2']],
                ['Path/To/Validator2', ['validatorParam1', 'validatorParam2']]
            ]
        ];

        $validator1 = $this->getMockBuilder(Validator::class)->getMock();
        $validator1->expects($this->once())->method('validate')->willReturn(true);
        $validator2 = $this->getMockBuilder(Validator::class)->getMock();
        $validator2->expects($this->once())->method('validate')->willReturn(false);

        $transition->expects($this->exactly(2))
            ->method('validatorFactory')
            ->willReturnOnConsecutiveCalls($validator1, $validator2);

        $this->assertFalse($transition->validate($stateMachine));
    }

    public function testTrueIfAllValidatorsTrue()
    {
        /**
         * @var Transition|PHPUnit_Framework_MockObject_MockObject   $transition
         * @var StateMachine|PHPUnit_Framework_MockObject_MockObject $stateMachine
         */
        $transition   = $this->getMockBuilder(Transition::class)
            ->setMethods(['validatorFactory'])
            ->disableOriginalConstructor()
            ->getMock();
        $stateMachine = $this->getMockBuilder(StateMachine::class)->getMockForTrait();

        $transition->settings = [
            'validators' => [
                ['Path/To/Validator', ['validatorParam1', 'validatorParam2']],
                ['Path/To/Validator2', ['validatorParam1', 'validatorParam2']]
            ]
        ];

        $validator1 = $this->getMockBuilder(Validator::class)->getMock();
        $validator1->expects($this->once())->method('validate')->willReturn(true);
        $validator2 = $this->getMockBuilder(Validator::class)->getMock();
        $validator2->expects($this->once())->method('validate')->willReturn(true);

        $transition->expects($this->exactly(2))
            ->method('validatorFactory')
            ->willReturnOnConsecutiveCalls($validator1, $validator2);

        $this->assertTrue($transition->validate($stateMachine));
        $this->assertEmpty($stateMachine->getValidationError());
    }

    public function testValidatorsAsObjects()
    {
        /**
         * @var Transition|PHPUnit_Framework_MockObject_MockObject   $transition
         * @var StateMachine|PHPUnit_Framework_MockObject_MockObject $stateMachine
         */
        $transition   = new Transition("someTransition", [new State(100)], new State(200));
        $stateMachine = $this->getMockBuilder(StateMachine::class)->getMockForTrait();

        $validator1 = $this->getMockBuilder(Validator::class)->getMock();
        $validator1->expects($this->once())->method('validate')->willReturn(true);
        $validator2 = $this->getMockBuilder(Validator::class)->getMock();
        $validator2->expects($this->once())->method('validate')->willReturn(true);

        $transition->settings = [
            'validators' => [$validator1, $validator2]
        ];

        $this->assertTrue($transition->validate($stateMachine));

        $validator3 = $this->getMockBuilder(Validator::class)->getMock();
        $validator3->expects($this->once())->method('validate')->willReturn(false);
        $transition->settings = [
            'validators' => [$validator3]
        ];

        $this->assertFalse($transition->validate($stateMachine));
        $this->assertEquals(get_class($validator3), $stateMachine->getValidationError());
    }
}

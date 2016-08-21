<?php


namespace tests\models;

use PHPUnit_Framework_TestCase;
use ValidatedStatemachine\exceptions\StateDefinitionIncorrectException;
use ValidatedStatemachine\models\State;
use ValidatedStatemachine\models\Transition;

class StateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var State
     */
    protected $state;

    public function setUp()
    {
        $this->state = new State(mt_rand(0, 100));
    }

    public function testAddGetTransition()
    {
        $stateTo    = new State(mt_rand(101, 200));
        $transition = new Transition("testTransition", [$this->state], $stateTo);
        $this->state->addTransition($transition);

        $transitions = $this->state->getTransitions();
        $this->assertEquals('testTransition', $transitions['testTransition']);
        $this->assertCount(1, $transitions);
    }

    public function testAddNotPossibleTransition()
    {
        $stateTo          = new State(mt_rand(101, 200));
        $transitionCanNot = new Transition("testCanNot", [$stateTo], $this->state);

        $this->expectException(StateDefinitionIncorrectException::class);
        $this->state->addTransition($transitionCanNot);
    }

    public function testCan()
    {
        $stateTo = new State(mt_rand(101, 200));

        $transitionCan    = new Transition("testCan", [$this->state], $stateTo);
        $transitionCanNot = new Transition("testCanNot", [$stateTo], $this->state);
        $this->state->addTransition($transitionCan);

        $this->assertTrue($this->state->can($transitionCan));
        $this->assertFalse($this->state->can($transitionCanNot));
    }

    public function testToString()
    {
        $state = new State(mt_rand(101, 200));
        $this->assertEquals((string)$state, $state->stateId);
    }
}

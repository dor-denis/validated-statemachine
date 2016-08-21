<?php


namespace tests\events;


use PHPUnit_Framework_MockObject_MockObject;
use ValidatedStatemachine\events\EventDispatcherSingleton;
use ValidatedStatemachine\events\TransitionExecutedEvent;
use ValidatedStatemachine\events\TransitionFailedEvent;
use ValidatedStatemachine\models\Transition;
use ValidatedStatemachine\StateMachine;

class TransitionExecutedEventTest extends \PHPUnit_Framework_TestCase
{
    public function testExecutesEventWhenTransitionFired()
    {
        $called = false;
        EventDispatcherSingleton::getDispatcher()->addListener(
            TransitionExecutedEvent::EVENT_NAME, function (TransitionExecutedEvent $event) use (&$called) {
            $called = true;
            $this->assertNotEmpty($event->getStateMachine());
            $this->assertNotEmpty($event->getTransition());
        }
        );

        /**
         * @var StateMachine|PHPUnit_Framework_MockObject_MockObject $stateMachineMock
         * @var Transition|PHPUnit_Framework_MockObject_MockObject   $transitionMock
         */
        $stateMachineMock = $this->getMockBuilder(StateMachine::class)->getMockForTrait();
        $transitionMock   = $this->getMockBuilder(Transition::class)->disableOriginalConstructor()->getMock();

        $event = new TransitionExecutedEvent($stateMachineMock, $transitionMock);
        EventDispatcherSingleton::getDispatcher()->dispatch(TransitionExecutedEvent::EVENT_NAME, $event);

        $this->assertTrue($called);
    }

    public function testExecutesEventWhenTransitionFailed()
    {
        $called = false;
        EventDispatcherSingleton::getDispatcher()->addListener(
            TransitionFailedEvent::EVENT_NAME, function (TransitionFailedEvent $event) use (&$called) {
            $called = true;
            $this->assertNotEmpty($event->getStateMachine());
            $this->assertNotEmpty($event->getTransition());
        }
        );

        /**
         * @var StateMachine|PHPUnit_Framework_MockObject_MockObject $stateMachineMock
         * @var Transition|PHPUnit_Framework_MockObject_MockObject   $transitionMock
         */
        $stateMachineMock = $this->getMockBuilder(StateMachine::class)->getMockForTrait();
        $transitionMock   = $this->getMockBuilder(Transition::class)->disableOriginalConstructor()->getMock();

        $event = new TransitionFailedEvent($stateMachineMock, $transitionMock);
        EventDispatcherSingleton::getDispatcher()->dispatch(TransitionFailedEvent::EVENT_NAME, $event);

        $this->assertTrue($called);
    }
}

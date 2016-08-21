<?php

namespace ValidatedStatemachine\events;

class TransitionExecutedEvent extends TransitionEventAbstract
{
    const EVENT_NAME = 'transition.executed';
}

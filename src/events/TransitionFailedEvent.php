<?php

namespace ValidatedStatemachine\events;

class TransitionFailedEvent extends TransitionEventAbstract
{
    const EVENT_NAME = 'transition.failed';
}

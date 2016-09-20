<?php

use ValidatedStatemachine\example\ExampleStateMachineSpecification;

require "../vendor/autoload.php";
require "ExampleStateMachineSpecification.php";
require "StateMachineModel.php";

$model = new \ValidatedStatemachine\example\StateMachineModel();
$model->stateId = ExampleStateMachineSpecification::STATE_1;

echo "Model initialized. Current state: " . $model->stateId . PHP_EOL;
$canExecute = $model->canExecuteTransition(ExampleStateMachineSpecification::TRANSITION_FROM1_TO_2);

echo "Can execute transition " . ExampleStateMachineSpecification::TRANSITION_FROM1_TO_2 . ": " . ($canExecute ? "true" : "false") . PHP_EOL;

echo "Executing transition: " . ExampleStateMachineSpecification::TRANSITION_FROM1_TO_2 . PHP_EOL;
$model->executeTransition(ExampleStateMachineSpecification::TRANSITION_FROM1_TO_2);
echo "Transition executed. Current state: " . $model->stateId . PHP_EOL;

$canExecute = $model->canExecuteTransition(ExampleStateMachineSpecification::TRANSITION_FROM1_TO_2);
echo "Can execute transition " . ExampleStateMachineSpecification::TRANSITION_FROM1_TO_2 . ": " . ($canExecute ? "true" : "false") . PHP_EOL;

$model->stateId = 300;
$canExecute = $model->canExecuteTransition(ExampleStateMachineSpecification::TRANSITION_FROM3_TO_4);
echo "Can execute transition " . ExampleStateMachineSpecification::TRANSITION_FROM3_TO_4 . ": " . ($canExecute ? "true" : "false") . PHP_EOL;

echo "Setting validated property to false..." . PHP_EOL;
$model->shouldExecuteTransition = false;
$canExecute = $model->canExecuteTransition(ExampleStateMachineSpecification::TRANSITION_FROM3_TO_4);
echo "Can execute transition " . ExampleStateMachineSpecification::TRANSITION_FROM3_TO_4 . ": " . ($canExecute ? "true" : "false") . PHP_EOL;

echo "Failed validator: " . $model->getValidationError() . PHP_EOL;

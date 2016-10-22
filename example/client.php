<?php

use ValidatedStatemachine\example\ExampleStateMachineSpecification;

require "../vendor/autoload.php";
require "ExampleStateMachineSpecification.php";
require "StateMachineModel.php";

$model = new \ValidatedStatemachine\example\StateMachineModel();
$model->stateId = ExampleStateMachineSpecification::STATE_PENDING;

echo "Model initialized. Current state: " . $model->stateId . PHP_EOL;
$canExecute = $model->canExecuteTransition(ExampleStateMachineSpecification::TRANSITION_SHIP);

echo "Can execute transition " . ExampleStateMachineSpecification::STATE_SHIPPED . ": " . ($canExecute ? "true" : "false") . PHP_EOL;

echo "Executing transition: " . ExampleStateMachineSpecification::STATE_SHIPPED . PHP_EOL;
$model->executeTransition(ExampleStateMachineSpecification::TRANSITION_SHIP);
echo "Transition executed. Current state: " . $model->stateId . PHP_EOL;

$canExecute = $model->canExecuteTransition(ExampleStateMachineSpecification::TRANSITION_DELIVER);
echo "Can execute transition " . ExampleStateMachineSpecification::STATE_DELIVERED . ": " . ($canExecute ? "true" : "false") . PHP_EOL;

$canExecute = $model->canExecuteTransition(ExampleStateMachineSpecification::TRANSITION_DELIVERY_FAILED);
echo "Can execute transition " . ExampleStateMachineSpecification::TRANSITION_DELIVERY_FAILED . ": " . ($canExecute ? "true" : "false") . PHP_EOL;

$model->stateId = ExampleStateMachineSpecification::STATE_PENDING;
echo "Setting validated property to false..." . PHP_EOL;
$model->shouldExecuteTransition = false;
$canExecute = $model->canExecuteTransition(ExampleStateMachineSpecification::TRANSITION_SHIP);
echo "Can execute transition " . ExampleStateMachineSpecification::TRANSITION_SHIP . ": " . ($canExecute ? "true" : "false") . PHP_EOL;

echo "Failed validator: " . $model->getValidationError() . PHP_EOL;

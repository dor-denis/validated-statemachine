<?php
use ValidatedStatemachine\example\ExampleStateMachineSpecification;
use ValidatedStatemachine\visualization\html\HtmlVisualization;

require "../vendor/autoload.php";
require "ExampleStateMachineSpecification.php";
require "StateMachineModel.php";

$specification = new ExampleStateMachineSpecification;
$viz = new HtmlVisualization;
echo $viz->visualize($specification);

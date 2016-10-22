# Validated Statemachine for PHP

A simple statemachine with ability to validate transitions.

## Usage

You can add the State Machine behavior to any PHP class by using *StateMachine* trait provided by this package.

```PHP
class StateMachineModel
{
    use StateMachine;

    public $stateId = 100;

    protected function getStateProperty()
    {
        return 'stateId';
    }
    
    protected function getSpecification()
    {
        return new ExampleStateMachineSpecification();
    }
}
```

You'll need to implement two methods:
- ```getStateProperty()``` - returns a string with the name of property holding the state value
- ```getSpecification()``` - returns an instance of the class extending StateMachineSpecification

### State Property

If your project uses some kind of ActiveRecord approach to store models, the state value can be also persisted to the DB.
To set initial state of the state machine, set default value of your property to it.


### State Machine Trait

StateMachine trait offers you next methods:
- *setState()* - sets current state of the model
- *getState()* - gets current state of the model (as *State* instance)
- *getAvailableTransitions()* - gets available transitions of the current model (as array of *Transition* objects)
- *canExecuteTransition()* - whether it is possible to execute a transition given as a param
- *executeTransition()* - execute the transition, returns ```true``` or ```false``` upon success/fail.
- *getValidationError()* - returns the validator class name which failed during last transition attempt.

For example of usage, see example/ folder

### State Machine Specification

The state machine specification is basically a configuration of your state machine which lists all possible states and transitions 
between them.

The example is listed below

```PHP
class ExampleStateMachineSpecification extends StateMachineSpecification
{
    const STATE_1 = 100;
    const STATE_2 = 200;
    const STATE_3 = 300;
    const STATE_4 = 400;

    const TRANSITION_FROM1_TO_2 = "from_1_to_2";
    const TRANSITION_FROM3_TO_4 = "from_3_to_4";

    public function getStateDefinitions()
    {
        return [
            self::STATE_1 => [],
            self::STATE_2 => [],
            self::STATE_3 => [],
            self::STATE_4 => [],
        ];
    }

    public function getTransitionDefinitions()
    {
        return [
            self::TRANSITION_FROM1_TO_2 => [
                'from' => self::STATE_1,
                'to'   => self::STATE_2
            ],
            self::TRANSITION_FROM3_TO_4 => [
                'from' => self::STATE_3,
                'to'   => self::STATE_4,
                'validators' => [
                    ['app\Validator1', ['exampleParam']],
                    ['app\Validator2', []]
                ]
            ],
        ];
    }
}
```

We've set state names to numbers so we could later sort them in DB by priority. You can also use strings or that.

In *StateMachineSpecification* class, you need to implement two methods:
- *getStateDefinitions* - returns array where keys are state names and values are payloads of each state (will be available to you when you'll call the *getState()* methon on StateMachine model). Can be also an array of *State* objects
- *getTransitionDefinitions* - returns array where keys are transition names, and values are arrays with next obligatory keys:
    * *from* - the state name **from** which the model can go with this transition;
    * *to* - the state name **to** which the model can go with this transition;
    * *validators* - the class names or instances of classes implementing *Validator* interface, and params for them (discussed below)
    * Remark: can be also an array of *Transition* objects

### Validators

You can validate your transitions by mentioning classes implementing *Validator* interface in ```'validators'``` key of ```getTransitionDefinitions()```
result array.

While performing the transition, the validators are being iterated and executed. 
If at least one validator returns false in the ```validate()``` method, 
the ```$error``` property of the state machine model will tell you which validator failed, and the ```executeTransition()``` method will return
false

An example of the Validator:

```PHP
class Validator2 implements \ValidatedStatemachine\models\Validator
{
    public function validate(Transition $transition, $model)
    {
        return !empty($model->shouldExecuteTransition) && $model->shouldExecuteTransition === true;
    }
}
```

The parameters passed to validator are Transition object and the state machine model.

If some params are passed to validator (like *exampleParam* in Validator1 in the example), they will be passed to ```__constructor()```
method of the validator.

### Events

The Symfony's EventDispatcher events are thrown:
- *transition.executed* - when the transition has been successfully executed
- *transition.failed* - if the transition failed

To subscribe to events, use ```EventDispatcherSingleton::getDispatcher()``` object.
For more info on Symfony event dispatcher, check out it's documentation

### Visualization

The *HtmlVisualization* class allows you to create a visualization for your state machine using the [sigma.js](http://sigmajs.org/) library.
It is using ForceAtlas2 algorithm to distribute the states (the nodes of the graph) and then you can drag them as you like.
After that you can print the result.

![State machine](https://github.com/dor-denis/validated-statemachine/raw/master/example/vizualize.png)

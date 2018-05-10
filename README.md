# CQRS Lite - Write Model
[![Build Status](https://scrutinizer-ci.com/g/tomaszhanc/cqrs-lite-write-model/badges/build.png?b=master)](https://scrutinizer-ci.com/g/tomaszhanc/cqrs-lite-write-model/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/tomaszhanc/cqrs-lite-write-model/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/tomaszhanc/cqrs-lite-write-model/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/tomaszhanc/cqrs-lite-write-model/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/tomaszhanc/cqrs-lite-write-model/?branch=master)

Sometimes it happens that you have many different commands which should be handled as one command. On the beginning
it looks trivial: just create one command and pass it to `CommandBus`, then create another one and another one.
But what with validation for a user? We would like to validate all commands before handling the first one. What if 
we do it via PATCH request: sometimes we will need to handle only one command, sometimes many - it depends on a request.

## Handling multiple commands... or just one

Let's assume we have an action for PATCH request and we have two commands: `Rename` and `Describe`. Depends on a
request we want to create both of them or only one:

```php
public function editAction(Request $request, $id)
{
    $renameFactory = new CommandFactory('name');
    $renameFactory->createBy(function (array $data) use ($id) {
        return new Rename($id, $data['name']);
    });
    
    $describeFactory = new CommandFactory('description');
    $describeFactory->createBy(function (array $data) use ($id) {
        return new Describe($id, $data['description']);
    });
        
    $builder = new CommandsBuilder();
    $builder->addCommandFactory($renameFactory);
    $builder->addCommandFactory($describeFactory);
 
    // $request->request->all() returns all data from the request (something like $_POST)
    $commands = $builder->createCommandsFor($request->request->all()); 
            
    // here you can validate commands and then handle them via command bus
        
    foreach ($commands as $command) {
        $this->commandBus->handle($command);
    }
}
```

Method `CommandsBuilder::createCommandsFor()` will create only those commands which should be created regarding
if required fields are available (at least one of them) in the passed data (in the example in `$request->request->all()`).
Above example can be simplified by using `CommandsBuilderSupport` trait: 

```php
use CommandsBuilderSupport;

public function editAction(Request $request, $id)
{
    $this->addCommandFor('name')->createBy(
        function (array $data) use ($id) {
            return new Rename($id, $data['name']);
        }
    );

    $this->addCommandFor('description')->createBy(
        function (array $data) use ($id) {
            return new Describe($id, $data['description']);
        }
    );
    
    $commands = $this->commandsBuilder->createCommandsFor($request->request->all()); 
        
    foreach ($commands as $command) {
        $this->commandBus->handle($command);
    }
}
```

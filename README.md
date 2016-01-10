# Picotable

A small library to add storage to any object. It's goal is to help with 
rapid prototyping. 

It is not intended to replace a correctly specified and fully featured 
persistence layer and should certainly not be used in production code

# Scenario

You need to run up a quick prototype. Pulling in your microframework of choice is all well and good 
but adding a database connection is a PITA

Not any more! Just add picotable to your prototype and add persistence to your models super quick and easy. 

# Installation

Simply use composer to add picotable to your prototype

    composer require vanqard/picotable


# Usage

You've already build out a demo model class and you need to hook each model up to a database table row. 

Here are the steps to go through to get that working.

## Modify your model class

Supposing you have your POPO already to go, like this:

    class UserModel
    {
        private $name;
        private $email;
        private $age;
    }
    
To connect it to a database table, with picotable installed, you'd edit the model class like this

    use \Vanqard\Picotable\Traits\Storable as StorageTrait;
    use \Vanqard\Picotable\Interfaces\StorableInterface;
    
    class UserModel implements StorableInterface
    {
        /**
         * Properties set to 'public' just for illustration. Feel free to
         * make them private and provide getters and setters / mutators as required
         */ 
        public $name;
        public $email;
        public $age;
        
        // Add the following
        private $usersId;
        
        private $_columnMap = [
            "usersId" => "users_id",  // Maps local property to db column 
            "name", 
            "email", 
            "age"
        ];
        
        use StorageTrait;
    }

Note what we have added there. 

First of all, we've added a `$usersId` property to correspond to the primary key value of the table.

Secondly, we've added a `$_columnMap` property which declares which object properties should be persisted. Note how the first element of this array provides both a key and a value. In this instance, the key is the object property name and the value is the corresponding database table column name. This is to provide a translation for when the names differ. 

The other elements of this array (name, email and age) do not require this treatment since the names are identical in both the object and the database table. 

In any event, the good news is that you're now ready to persist your model objects. 

Well, almost. 

You need to set up the connection somewhere. You do this with the `Connector` object. Instantiating a `Connector` instance would normally be done somewhere in your bootstrap process before any requests are dealt with. 

Here's the code to do that.

    $connector = new \Vanqard\Picotable\Connector($dsn, $user, $pass);
    
As you can see, you need to provide connection parameters for the connector's construct. Here are some examples

    // SQLite
    $dsn = "sqlite:prototype.db";
    $user = $pass = null;
    
    // MySQL
    $dsn = "mysql:host=localhost;dbname=prototype";
    $user = "root";
    $pass = "secret";
    
Ok, looking good. 

So how do we connect a model object to a database table?

Like this

    $myUser = new UserModel();
    $connector->connect($myUser, 'users');  // Assume db table name is 'users'

That's it? Why yes, yes it is. Now you can do whatever you want with your model object

### Saving a new model

    $myUser = new UserModel();
    $connector->connect($myUser, 'users');
    
    $myUser->name = "joe";
    $myUser->email = "joe@example.com";
    $myUser->age = 42;
    
    $myUser->_save();
    
    echo $myUser->usersId;   // Just to confirm that we've saved successfully

### Loading a model from the db

    $myUser = new UserModel();
    $connector->connect($myUser, 'users');
    
    // Pass the primary key value to the _load method
    $myUser->_load(1);
    
    echo $myUser->name;  // Outputs 'joe' if joe is the name identified by the primary key value "1"

### Updating the db with a model

    $myUser = new UserModel();
    $connector->connect($myUser, 'users');
    
    $myUser->_load(1); // Loads the row for 'joe'
    
    $myUser->name = "mary";
    $myUser->email = "mary@example.com";
    $myUser->age = "27";
    
    $myUser->_save(); // Save knows when to insert and when to update
    
### Deleting a row in the db

    $myUser = new UserModel();
    $connector->connect($myUser, 'users');
    
    $myUser->_load(1); // Loads the row for 'joe'
    
    // ... sometime later
    
    $myUser->_delete(); // Row is deleted for 'joe'
    
    
# Todo

 * Error handling - There's virtually none in here at the moment
 * Unit tests - These are also woefully absent but hey, this is the first commit. (And it works on my box) 
 
# Security issues

If you find any security problems with this code, please contact the author directly at [thunder@vanqard.com](mailto:thunder@vanqard.com)





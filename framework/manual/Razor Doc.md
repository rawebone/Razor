# Razor Framework

Razor is a minimalist web application framework which provides a bridge
between "classic" and "modern" PHP development techniques. Its goal is to help
its users to create simple, testable, maintainable applications that conform to
modern PHP techniques and standards. These techniques, such as test driven design
and dependency injection, help to build in quality but often require complex
architectures as a result. Razor tries to simplify this into a pragmatic solution
while retaining the benefits.

This document covers the basic concepts of Razor, explaining where they have come
from, and how they benefit your development cycle; this is summarised by an
example application walk-through.

## When and Why to use a Framework

There is no more elegant a way to sum this up than by reference to the following
post on [Reddit by Phil Sturgeon](http://www.reddit.com/r/PHP/comments/234shb/pure_php_vs_using_a_framework/cgtqw2u):

    "The level at which to attack a problem will never be agreed upon." or something. Jason Judge, redditor.

    Pure PHP is what Rasmus [Lerdorf] will tell you to use, because ultimate speed and ultimate flexibility. You can do exactly what you want, not have to worry about public bug announcements affecting your code and performance will be marginally improved because theoretically your pure PHP is doing less than a framework is.

    All of that is "theoretically" because it assumes that your code is:

        * Secure
        * Efficient
        * Well architected
        * Well optimized

    In my experience, most folks using "pure PHP" are not hitting any of those criteria.
    They are usually (in my experience) dickheads who think they are better than everyone else,
    and who take a really long time to produce really complicated PHP code which is hackable as fuck.

    Not every time for sure, and when you are producing an app at huge scale you may want to look at
    removing the framework to save that 140ms of bootstrap it has to do, but again, if your code isn't
    quicker than the framework code then you've just wasted everyones time.

    You have to work out at what point you accept help.

        * Frameworks
        * Packages
        * PECL extensions

    They are all extra code. You can't do it all, you don't need to and you shouldn't.

Frameworks provide a way of structuring our applications which makes it easier
for other developers to understand what is transpiring which improves our ability
to find where an error is occurring. The performance issue is these days primarily
redundant - the difference in the time to serve requests between pure PHP and
a framework are in the microseconds and if there are performance issues then there
are a number of techniques that can be applied for increasing responsiveness:

* Adding indexes on tables where required
* Improving query performance
* Better table design
* Minimising CSS, JavaScript and Image assets
* Proper caching of assets using Expiry and E-Tag headers
* Using a CDN URL to enable the downloading of assets asynchronously
* Reducing the amount of disk hits/parsing required by using OPCode caching
* Using a fast process manager to negate performance loss because a PHP process
  has to be spawned and configured on each request
* Using memory caches for frequently served data/files
* In extremely complex systems with lots of files, a compiled cache of the most
  frequently required classes can be created using automated tools to reduce
  disk hits significantly

The question of performance is a grey area which requires tuning per application
to decided where problems are occurring and handle them. The ideal that pure PHP
is faster is naive; ideas like those above apply to any kind of web application
regardless of the language or framework choice. The playing field levelled, pure
PHP doesn't stand up when creating web applications because it leads to messy,
difficult to maintain code which by and large cannot be tested.


## Framework Concepts

### HTTP and Handlers

HTTP can be summarised as thus: an **Agent** (such as a web browser) makes a
**Request** to a web server, and the server returns a **Response**. The type of
request made varies with the requirements of the application but the most
common is the `GET` request, where the agent requests the data of a resource.
An example of a `GET` request and response can be seen by opening your web
browser and navigating to `www.google.co.uk`.

When we design a web application, the web server delegates the processing
of the request and the response to our scripts whose job is to interpret
the information sent by the agent and provide the appropriate data; Razor
provides a structure for this interpretation via a mechanism called a
**Handler**. For example, if we have an application hosted at the URL
`www.example.com/index.php`, and we want to handle `GET` requests then our
 index.php file would look like:

```php
<?php

// File: public/index.php

require_once(__DIR__ . "/../framework/razor.php");

get(function () 
{
	// Do something
});

run();
```

The first step we take is to load the framework (`require_once(__DIR__ . "/../framework/razor.php")`)
which provides us with the `get()` and `run()` functions. Then we call the `get()`
function and pass it our **Handler** - which is an anonymous function, or
**Closure** - which will be invoked when a `GET` HTTP request is made to the
index file. Lastly we call the `run()` function which tells the framework to
match the request to and invoke a handler.

So to recap we load the Razor framework, pass in our logic to be handled when
a `GET` request occurs and finally we run the application. It is also possible
to use multiple handlers within the same file to achieve your desired functionality,
below is a example of a file that handles both a `GET` and `POST` requests:

```php
<?php

// File: public/index.php

require_once(__DIR__ . "/../framework/razor.php");

get(function () 
{
	// Display form
});

post(function () 
{
	// Do something with form data
});

run();
```

We can setup handlers using the mechanism described above for the following
HTTP request types:

* `get`
* `post`
* `head`
* `options`
* `delete`
* `put`
* `patch`


### Services

One of the biggest challenges in application is managing changes; today
our database is a MySQL server, tomorrow it might be an MSSQL backend. Our
applications have to be tolerant of change so that we can expedite fixes
and new functionality to our users. In applications that follow the more
classic PHP approach, we would start adding conditions to our code -
potentially in multiple places in the app - to meet these new requirements,
lowering the concision of the code and increasing the potential for problems
such as changes not being made in multiple places.

In addition, performant web applications are those which load the least
amount of resources as possible to reduce disk hits/parse time to get the
job done. In classic PHP this normally dictates that we have to spend the
first twenty lines of code including and requiring files, making changes
to API's more difficult. Alternatively developers try to reduce the
amount of files present in the application which greatly reduces their
ability to maintain it as it slowly descends into a spaghetti code monster.

Finally, in classic PHP there's no good way to test the scripts to make
sure they work because your application is totally live - your script
creates object instances, loads files and such manually and so is too
_tightly coupled_ to it's environment to test via automation.

All of these problems can be mitigated in Razor through a system called
**Services**. The best way to show how this works is by example; say we
setup our handler in `index.php` and we want to send out a greeting to
the agent who sent the request:

```php
<?php

// File: public/index.php

require_once(__DIR__ . "/../framework/razor.php");

get(function ($response) 
{
	$response->general("Hello, World!")->send();
});

run();
```

As you can see, in our `GET` request handler we now have a parameter called
`response` upon which we call the methods `general()` and `send()`. We are
able to do this because of a technique called **Dependency Injection**; in
this example, when the framework invokes the handler it first reads in its
parameters and matches them to the _names of defined services_. It then
_injects the required services_ into the handler so that it can perform its
task. We can say that we have _de-coupled the handler from its dependencies_.

The framework itself provides the service called `response` used in this example
but this functionality comes into it's own primarily because you can define
your own services which can be injected through this same mechanism. For example:

```php
<?php

// File: application/bootstrap.php

services(array(
    "document" => function ()
    {
        return new XmlDocumentReader();
    }
));
```

Here we define a service called `document` which is assigned a closure. When
the closure is invoked, an `XmlDocumentReader` object. Say we want to consume
this service in our application:

```php
<?php

// File: public/document-reader.php

require_once(__DIR__ . "/../framework/razor.php");

get(function ($document)
{
    var_dump($document); // object "XmlDocumentReader"
});

run();
```

This may seem a little counter-intuitive at first, but let's take a more
detailed look at what we have done here:

* We have defined a callback which return an object based upon it's name
* The object will not be created until it's needed meaning memory will not be
  used unnecessarily
* Until it is created, the file which contains the object will not be included
  and so redundant disk hits avoided
* If we want to test the functionality of the handler later on, we can swap out
  the service with a test dummy to validate behaviour

In addition, we can more gracefully handle change. Say we decide that XML is too
verbose and so we switch to using JSON documents, we can now create an object
with the same interface as the `XmlDocumentReader` called `JsonDocumentReader`
and make the change in our application by:

```php
<?php

// File: application/bootstrap.php

services(array(
    "document" => function ()
    {
        // return new XmlDocumentReader();
        return new JsonDocumentReader();
    }
));
```

And so without and changes to our handler, we can change to using JSON documents.

As you can see within our get logic we create a variable holding a simple hello world string and then call a method
'send' on our response **Service** passing in the text we wish to send.

- To Do: List of services and what they do

### Controllers

We have already used a **Controller** in the course of this document, lets take a look at the first example code demonstrated.

```php
<?php

// File: public/index.php

require_once(__DIR__ . "/../framework/razor.php");

get(function () 
{
	// Do something
});

run();
```

We have already covered the functionality in this example but note the comment on the 2nd line (File: `public/index.php`)
this denotes the location of this file and as you can see this file exists inside a publicly accessible folder 'public'.

This entire file is the **Controller** and is essentially a entry point into your razor application, as we have seen before we
use **Handlers** to determine the logic we use with the type of browser request, but a **Controller** is used to hold these
**Handlers** and denote the logic we execute based upon the location of the browser request.

- To Do: Could use more clarity

So now we have **Handlers**, **Controllers** and Razor inbuilt **services** (more on custom **Services** later), so lets take a look
at the folder structure as we currently know it (I have excluded folders we have yet to cover)

```

/framework                  <-- The Razor Framework
/public                     <-- Controllers, anything that needs to be publicly accessible
    /assets                 <-- CSS Files, JavaScript Files, static images etc

```

Firstly we have the framework folder, this folder contains the Razor framework. Next we have the public folder, this folder and
all its sub folders/files are publicly accessible, **Controllers** for your application reside within this folder.
Finally I have included the assets folder, this folder is yours to store assets your application may need to publicly
host (CSS files, JS files, images etc).

We have only focused on the folders covered so far but it is important to understand and implement a structure to your application
in a uniform way to help with maintenance and collaboration. We will extend the folder structure later to cover more of the workflow.

### The Application BootStrap

As you develop your application you'll find that you want to define your own custom **Services**, for this we have the `application/bootstrap.php`
file which is called automatically when we load the framework, first lets look at a empty bootstrap.

```php
<?php

// File: application/bootstrap.php

services(array(
    
));

```

As you can see the bootstrap calls a function called 'services' and passes a array, curently this array is blank however it is
here we define our **Services**. All our **Services** use associative arrays and as such are defined in two parts, the first part is the
name for this **Service** and the second part is a function to be executed when this **Service** is used (normally this function simply returns
a object).

Lets create a **Service** called "hello" to store a value of "Hello, World!".

```php
<?php

// File: application/bootstrap.php

services(array(
    "hello" => function ()
    {
        return (object)array("text" => "Hello, World!");
    }
));

```

Normally we would create this object as a **Model** outside of this file (more on this later) but for now we have directly created
our object in the array, now we can use this **Service** in our `public/index.php` **Controller**.

```php
<?php
// File: public/index.php

require_once(__DIR__ . "/../framework/razor.php");

get(function ($response, $hello) 
{
	$response->general($hello->text)->send();
});

run();
```

As you can see by including "$hello" as a argument in our get request callback function Razor has automatically injected the hello
**Service** into our callback ready for use.

More importantly **services** can consume other **services** if we modify the `application/bootstrap.php` to.

```php
<?php

// File: application/bootstrap.php

services(array(
    "hello" => function ($world)
    {
        return (object)array("text" => "Hello" + $world->text);
    }
	"world" => function ()
    {
		return (object)array("text" => "World!");
	}
));

```

we have now split the "$hello" **Service** to multiple **Services** one referencing the other, this is an incredibly powerful feature
of the framework which allows you to expose and compose **Services** in your application with ease and modify **Services** without having
to modify references to these **Services** in your **Controllers**.

Lets take another look at the folder structure as we know it.

```
/application                <-- Where you're application is configured

/framework                  <-- The Razor Framework
/public                     <-- Controllers, anything that needs to be publicly accessible
    /assets                 <-- CSS Files, JavaScript Files, static images etc

```

Now we have a new folder "application" this folder is used to hold configurations for our application, currently we only know of one configuration
`Bootstrap.php` however as you progress through this document you will learn of other configuration files.

### Testing

- To Do: What are tests
- To Do: Why and how we use them 
- To Do: Examples
- To Do: Folder Structure Part V









## WorkFlow

- To Do: What is a WorkFlow
- To Do: Summary of the Razor WorkFlow (Steps)

### Example Project

- To Do: Summary of what we plan to achieve
- To Do: Summary of he example project

#### User Requirements

- To Do: What we hope to achieve in this step
- To Do: Run through this step with our example Project

#### Functional Requirements

- To Do: What you hope to achieve in this step
- To Do: Run through this step with our example Project

#### Project Structure

- To Do: What we mean by structure
- To Do: Run through this step with our example Project

#### From test to functionality (Red, Green, Refactor)

- To Do: What you hope to achieve in this step
- To Do: Run through part of the application with this step
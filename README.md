i-tide
======

As the Norwegian rules for when it is allowed to sell alcoholic beverages in 
stores and in [_Vinmonopolet_](http://www.vinmonopolet.no/) are quite 
complicated, I decided to steal  [Nikolai Luthman](https://github.com/nuth)'s 
original idea and implemented a nifty calendar app to help me keep track of 
the sales hours.

However, the original app proved hard to maintain and wasn't really flexible, 
and since I wanted to to a micro web framework in PHP for fun, I made this new
RESTy back-end. It's a overkill for something as simple, but it is a fun toy
project as I don't get a lot of time to do web development any more. 

The name "_i tide_" is Norwegian for "_in time_".

Calendar UI
-----------
The calendar-like front-end is currently under development. It will have a 
similar functionality as the old-one, but it will also have live countdown
timers.

I will try to make it with a responsive design, as the old version didn't 
quite work on small screens such as mobile phones.

RESTy API
---------
I've implemented a REST-like (hence _RESTy_) API for this version. Seeing how
it's all date manipulation, it is really an overkill, but it is convenient if
you want to make a small script and can't be bothered with doing the logic
yourself. As the functionality is specific to Norwegian rules for sales hours,
I haven't bothered with proper localisation and stuff like that. 
The content language is Norwegian (no), but end-points, datatypes, data structures
etc. are named in English. 
All data is UTF-8 encoded, which is relevant for the Norwegian specific characters:
Æ æ Ø ø Å å.
Dates are formatted as `YYYY-MM-DD`, time is 
formatted as `HH:mm` for sales hours or `HH:mm:ss` for current time and countdown timers.

Most of the end-points currently supports 
delivering data in three different data formats: XML, JSON or plain text. 
The client can decide which content type it wants by setting the HTTP `Accept` 
header to one of the following: `application/xml`, `application/json`, 
`text/xml`, `text/json` or `text/plain`. The `Accept` header can be overriden 
by concatenating a `.xml`, `.json` or `.txt` to the end-points, something which
is convenient for debugging in a browser.
Note that the plain text format isn't really intended for parsing.

Micro framework
---------------
This project was (mostly) motivated by creating my own micro web framework.
Currently it has an extremely limited set of features, and only handles simple
HTTP GET requests. It has no functionality for handling timezones, different 
languages, different encodings etc.

It follows a simple MVC-pattern. REST resource end-points are implemented as
controllers, and are responsible for invoking the appropriate model classes,
parse HTTP request data and return the appropriate view. Controllers and views
have to extend the Controller and View base classes respectively, while model
classes can be whatever. The main idea is to separate presentation from business
logic, although the business logic for this application is limited to simple 
date manipulation and timestamp arithmetics. In retrospect, I should probably
have named controllers _Resources_ instead.

Routes/end-points are currently configured statically in the `.htaccess` file,
although I plan on making this more dynamic in a later version.
The `rest.php` script contains some bootstrapping for autoloading and 
instantiating the correct controller class as well as return a properly formatted
(albeit minimal) HTTP response to the client. The `ctrl.php` and `view.php` 
files contain the Controller and View base classes respectively. I've made
some globally accessible utility functions in `util.php`, but right now they
are mostly for date parsing and translating months and weekdays to Norwegian.

In order to get the idea of how things work, I suggest looking in the `ctrl/` 
and `view/` directories directory and have a look at the code. I apologise
for not having documented the code better or even provided descriptive 
comments, but as stated it is just a toy project. The main idea is that a
controller registers one (or more) request handlers which are responsible
for loading the appropriate model and passing it on to the view. The 
controllers must also in the request handler register the available views.

The views then extract data from the model and encode it in the correct format.
Currently I only support XML, JSON and plain text. For my application, each
view implements all three types, but one can choose not to support all three
types in a view and have multiple views for a resource.

I used PHP namespaces for this framework, but it proved to be more of an
hassle than a benefit really.


﻿# OnlineShop
*OnlineShop* provides a basic skeleton for database exercises, that use PDO for accessing MariaDB from PHP.

Templates and CSS are complete, because this part is taught in different lessons. So these exercises can focus on
database access with PHP Libraries.

Visit us at https://www.fh-ooe.at/en/hagenberg-campus/studiengaenge/bachelor/media-technology-and-design/

# Used technologies and requirements

The exercises have been developed with [Vagrant](https://www.vagrantup.com/) and [Virtualbox](https://www.virtualbox.org/). 
A [Vagrantfile](https://github.com/Digital-Media/hgb-phpdev-base) with the installation of the web environment is hosted on GitHub.
But *OnlineShop* can be installed in a different environment as well.

PHP 7.1 is required to run the examples.

* [HTML5](https://www.w3.org/TR/html5/)
* [CSS3](https://www.w3.org/Style/CSS/specs)
* [PHP 7.0](http://php.net/manual/en/migration70.new-features.php)
* [PHP 7.1](http://php.net/manual/en/migration71.new-features.php)
* [MariaDB](https://mariadb.org/)
* [PDO-Datenbankschnittstelle](http://php.net/manual/en/book.pdo.php)
* [TNormform](https://github.com/Digital-Media/normform)
* [TWIG Templates](https://twig.symfony.com/)
* [CSS3 Flexbox](https://www.w3.org/TR/css-flexbox-1/)
* [PHP-FIG PSR: PHP Standards Recommendations](https://www.php-fig.org/psr/)


The files, that have to be completed for the exercises, are stored in the subdirectory src/exercises.
Each lesson has its own subdirectory. For one exercise only files in this subdirectory have to be completed.
All other files are for reference.

Sample solutions are included with require from a solution folder. The git repostitory, that holds the solution is private.
These parts can be ignored for your own solution or deleted completely.
Do not include these files, but work directly in the files in src/exercises to complete your solution. TODOs will guide you, what to do for completing the solution.

For example:  
    
    /*--
    require '<path-to-solution>/index/construct.inc.php';
    //*/

Given parts of the solution are marked as seen below. These parts ensure, that the code works without PHP runtime errors, even before the solution is completed.
     
     //##
     return true;
     //*/
     
For example a fake login is implemented in a way, that you can login without given user credentials. 
To complete the solution you have to implement the database access to validate the given user credentials.
Keep these parts of the code, they can be part of your final solution, if you put them on the right place in your own code.

To demonstrate, how the skeleton works, the comments are set, as seen in the examples avove.
If you have access to the solution repository you can demonstrate, how the pattern solution works.
In this case the comments are switched to

    //--
    require '<path-to-solution>/index/construct.inc.php';
    //*/

and

     /*##
     return true;
     //*/

Shell scripts are used to switch comments. They are stored in a private git repository, that holds the solution. 
They use GNU sed to replace the comments.


## Structure of this Repository

Folder | Description
--- | ---
``htdocs`` |Frontend stuff. Files called by Webserver, that create the objects with the real implementation, templates and css
``htdocs/css`` | A set of predefined styles to be used with *NormForm*. Include ``main.css`` to use it.
``htdocs/templates`` | HTML templates for the Smarty template engine used in ``/src/*.php``.
``htdocs/templates_c`` | Output folder for Smarty's compiled templates.
``src`` | Classes implemented for *OnlineShop*. Including a dbdemo for [NormForm](https://github.com/Digital-Media/normform) and DBAccess.  The Trait Utilities provides static helper methods to be used in any context.
``src/exercises`` | Classes to be implemented for *OnlineShop* exercises.
``vendor`` | Third party libraries installed with composer: [TNormform](https://github.com/Digital-Media/normform), [Smarty Templates](http://www.smarty.net/), Javascript Libraries ...

A basic class diagramm for OnlineShop (built with http://www.umlet.com/umletino/

![OnlineShop Klassendiagramm](src/KlassenDiagrammOnlineShop.png "OnlineShop Class Diagram")

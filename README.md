# OnlineShop
*OnlineShop* provides a basic skeleton for database exercises.

Templates and CSS are complete, because this part is taught in different lessons. So these exercises can focus on
database access with PHP Libaries.

Visit us at https://www.fh-ooe.at/en/hagenberg-campus/studiengaenge/bachelor/media-technology-and-design/

# Used technologies and requirements

The exercises have been developed with [Vagrant](https://www.vagrantup.com/) and [Virtualbox](https://www.virtualbox.org/). 
A [Vagrantfile](https://github.com/Digital-Media/hgb-phpdev-base) with the installation of the web environment is hosted on GitHub.
But *OnlineShop* can also be installed in a different environment.

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


Die Übungen, die umzusetzen sind, liegen in einem Unterverzeichnis von src/exercises. 
Für jede Übung gibt es ein eigenes Unterverzeichnis. Nur an den Files in diesem Verzeichnis sind Änderungen vorzunehmen.

Musterlösungen werden mit require aus einem privaten Repository eingebunden.
Diese Abschnitte können für die Lösung ignoriert oder vollständig gelöscht werden.
Statt diese Files einzubinden ist direkt in den Vorlagenfiles eine eigene Lösung an Hand der TODOs zu erstellen.

Zum Beispiel:  
    
    /*--
    require '<path-to-solution>/index/construct.inc.php';
    //*/

Vorgegebene Codeteile die wie folgt gekennzeichnet sind, dienen dazu die Vorlage ohne PHP-Fehler lauffähig zu machen.
     
     //##
     return true;
     //*/
     
Zum Beispiel wird eine erfolgreiche Authentifizierung vorgetäuscht, damit ein Login auch ohne Datenbankzugriff bereits für
den Erfolgsfall funktioniert. Diese Codeteile müssen behalten werden, an der richtige Stelle im eigenen Code verwendet
oder entsprechend angepasst werden, damit sie der Aufgabenstellung entsprechen.

Durch Ein- und Auskommentieren der Lösung bzw. der vorgegebenen Codeteile kann sowohl die Funktionalität der 
Vorlage als auch der Lösung einfach demonstriert werden. 
Das Umschalten erfolgt über Linux Shell-Scripten, die im Lösungsrepository liegen und dazu GNU sed nutzen. 


## Structure of this Repository

Folder | Description
--- | ---
``htdocs`` |Frontend stuff. Files called by Webserver, that create the objects with the real implementation, templates and css
``htdocs/css`` | A set of predefined styles to be used with *NormForm*. Include ``main.css`` to use it.
``htdocs/templates`` | HTML templates for the Smarty template engine used in ``/src/*.php``.
``htdocs/templates_c`` | Output folder for Smarty's compiled templates.
``src`` | Classes implemented for *OnlineShop*. Including a dbdemo for [NormForm](https://github.com/Digital-Media/normform) and DBAccess.  The Trait Utilities provides static helper method to be used in any context.
``src/exercises`` | Classes to be implemented for *OnlineShop* exercises.
``vendor`` | Third party libraries installed with composer: [TNormform](https://github.com/Digital-Media/normform), [Smarty Templates](http://www.smarty.net/), Javascript Libraries ...

A basic class diagramm for OnlineShop (built with http://www.umlet.com/umletino/

![OnlineShop Klassendiagramm](src/KlassenDiagrammOnlineShop.png "OnlineShop Class Diagram")

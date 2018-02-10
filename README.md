# onlineshop 2018.0
Der Onlineshop stellt das Grundgerüst für Übungen im Datenbankbereich zur Verfügung.

Templates und CSS werden vollständig zur Verfügung gestellt, weil dies in anderen Vorlesungs- und Übungsblöcken
vermittelt wird. Dadurch soll es möglich sein, sich auf den eigentlichen Lerninhalt Datenbankzugriff von PHP aus
zu konzentrieren.

Besuchen sie uns unter https://www.fh-ooe.at/en/hagenberg-campus/studiengaenge/bachelor/media-technology-and-design/

Verwendete Technologien und Vorraussetzungen

Für das Übungsscenario wurde mit [XAMPP](https://www.apachefriends.org/de/index.html) entwickelt. Der OnlineShop lässt sich aber auch unter anderen Umgebungen
installieren.

* [HTML5](https://www.w3.org/TR/html5/)
* [CSS3](https://www.w3.org/Style/CSS/specs)
* [objectorientiertes PHP](http://php.net/)
* [MariaDB](https://mariadb.org/)
* [PDO-Datenbankschnittstelle](http://php.net/manual/en/book.pdo.php)
* [TNormform](https://github.com/Digital-Media/TNormform)
* [Smarty Templates](http://www.smarty.net/)
* [jsOnlyLightbox](https://github.com/felixhagspiel/jsOnlyLightbox)
* [CSS3 Flexbox](https://www.w3.org/TR/css-flexbox-1/)
* [jquery](https://jquery.com/), als Teil der Übung, aber nicht als Teil des Grundgerüstes 
* [PHP-FIG Namenskonventionen](http://www.php-fig.org/bylaws/psr-naming-conventions/)


Die Abschnitte, die umzusetzen sind, liegen in einem eigenen Repository und werden über require_once eingebunden, wenn
das Lösungsrepository in einen Unterordner solution des OnlineShop kopiert wird.
Zum Beispiel:  
    /\*--
    require_once 'solution/index/construct.inc.php';
    //\*/  
Durch ein- und auskommentieren der Lösung kann sowohl die Funktionalität der Vorlage und der Lösung einfach demonstriert werden.
Das Umschalten erfolgt über Linux Shell-Scripten, die im Lösungsrepository liegen und dazu GNU sed nutzen.
Die auskommentierten Codezeilen können für eine Grundfunktionalität ohne Datenbankzugriffe vollständig entfernt werden.
Das Lösungsrepository ist nicht öffentlich. Der entsprechende Code ist für die volle Funktionalität mit Datenbankzugriff
in den Übungen zu ergänzen.

Ein grobes Klassendiagramm für den OnlineShop (erstellt mit http://www.umlet.com/umletino/

![OnlineShop Klassendiagramm](src/KlassenDiagrammOnlineShop.png "OnlineShop Klassendiagramm")

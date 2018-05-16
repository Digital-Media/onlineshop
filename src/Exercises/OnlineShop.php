<?php
namespace Exercises;

use Fhooe\NormForm\Core\AbstractNormForm;
use Fhooe\NormForm\Parameter\GenericParameter;
use Fhooe\NormForm\Parameter\PostParameter;
use Fhooe\NormForm\View\View;
use DBAccess\DBAccess;
use Utilities\Utilities;

/**
 * Class Shop implementiert die Startseite (index.php) des OnlineShop
 *
 * Die Seite index.php setzt auf der ojectorientieren Klasse TNormform und den Smarty-Templates von IMAR aus HM2 auf.
 * Weiters benötigt es die Klasse DBAccess für Datenbankzugriffe, die die Klasse FileAccess von IMAR ersetzt.
 * Durch die Verwendung von PDO Prepared Statements sind keine weiteren Maßnahmen gegen SQL-Injection notwendig.
 *
 * Diese Seite listet die Produkte des OnlineShops in einer Liste auf, die durchblättert werden kann.
 * Über die Konstante DISPLAY wird gesteuert, wieviele Produkte pro Seite angezeigt werden.
 * Über ein Suchfeld kann ein GET-Request abgesetzt werden, der die Anzahl der Treffer einschränkt.
 * Mit Shop::addToCart() können Produkte über einen POST-Request in den Warenkorb Tabelle onlineshop.cart gelegt werden.
 *
 * Die Klasse ist final, da es keinen Sinn macht, davon noch weitere Klassen abzuleiten.
 *
 * @author Martin Harrer <martin.harrer@fh-hagenberg.at>
 * @package onlineshop
 * @version 2016
 */
final class OnlineShop extends AbstractNormForm
{
    // make trait Utilities accessible via $this->
    use Utilities;
    /**
     * Constants for a HTML attribute in <input name='ptype' id='ptype' ... >, <label for='ptype' ... >
     * --> $_POST[self::PTYPE] and GET-Parameters, sent by links
     *
     *
     * @var string START Key für $_GET- und $_SESSION-Eintrag den Starteintrag
     *                   für den angezeigten Ausschnitt der Produktliste festlegt
     *                   @see src/basetemplates/pagination.html.twig
     * @var string SORT Key für $_GET- und $_SESSION-Eintrag, der die Sortierreihenfolge bestimmt
     * @var string SEARCH Key für $_POST-Eintrag für Suchfeld
     * @var string PID Key für $_POST-Eintrag für den Bestellbutton
     */
    const START = 'start';
    const SORT = 'sort';
    const SEARCH = 'search';
    const PID = 'pid';
    const DISPLAY = 2;

    /**
     * Hilfsvariablen, um Werte zwischen den Methoden auszutauschen
     *
     * @var string $start Startwert in der LIMIT-Klausel,
     * @var string $sort gibt an nach welcher Spalte die ausgegebene Produktliste sortiert werden soll
     * @var string $search gibt das Suchkriterium an, das die Produktliste einschränkt
     * @var string $oldsearch Hilfsvariable, um den alten Wert von $search zwischenzuspeichern,
     *                        um ihn später mit dem neuen vergleichen zu können.
     * @var array $pid ProduktID, des Produkts, das mit AddToCart bestellt wurde.
     * @var string $order_by beinhaltet die ORDER BY-Klausel für die sortierte Produktliste
     * @var string $rowcount Anzahl der Produkte in der Tabelle onlineshop.produkt, die dem Suchkriterium entsprechen.
     * @var string $pagecount Anzahl der Seiten, auf denen die Produktliste dargestellt werden kann,
     *                        abhängig von der Konstante DISPLAY
     * @var string $startprevious Startwert für die LIMIT-Klausel für das Select für den Previous-Link
     * @var array $pagenumber Array mit den Startwerten aller Seiten,
     *                        die über einen Link der mit der Seitennummer benannt ist,
     *                        direkt angesprungen werden können
     * @var string $current_page Hilfsvariable, die die Seitennummer der aktuell
     *                           angezeigten Seite der Produktliste beinhaltet
     * @var string $startnext Startwert für die LIMIT-Klausel für den Select für den Next-Link
     *
     */
    private $start;
    private $sort;
    private $search;
    private $oldsearch;
    private $pid;
    private $order_by;
    private $rowcount;
    private $pagecount;
    private $startprevious;
    private $pagenumber;
    private $current_page;
    private $startnext;

    /**
     * @var string $dbAccess  Database handler for access to database
     */
    private $dbAccess;

    /**
     * Shop constructor.
     *
     * Calls constructor of class AbstractNormForm.
     * Creates a database handler for the database connection.
     * The assigned constants can be found in src/defines.inc.php
     *
     * @param View $defaultView Holds the initial @View object used for displaying the form.
     *
     * @throws DatabaseException is thrown by all methods of $this->dbAccess and not treated here.
     *         The exception is treated in the try-catch block of the php script, that initializes this class.
     */
    public function __construct(View $defaultView)
    {
        parent::__construct($defaultView);
        /*--
        require '../../onlineshopsolution/index/construct.inc.php';
        //*/
        // Add the images to our view since we can't do this from outside the object
        $this->currentView->setParameter(new GenericParameter("startKey", self::START));
        $this->currentView->setParameter(new GenericParameter("pageArray", $this->fillpageArray()));
    }

    /**
     * Validates the user input after a order request with one of the "AddToCart" buttons.
     *
     * Auch wenn die pids im Template durch das PHP-Script index.php eingetragen werden,
     * muss der Input als Benutzerinput gewertet werden,
     * weil man nicht weiß, ob der Nutzer zum Senden der Bestellung den Request mit
     * entsprechenden Tools noch manipuliert.
     * Für jede pid ist ein eigener Button implementiert, daher wird jede pid im Array $_POST['pid']
     * in einem eigenen Eintrag gespeichert.
     * Mittels Shop::isValidPid() wird jede pid im Array $_POST['pid'] auf Gültigkeit geprüft.
     * Fehlermeldungen werden im Array $errorMessages[] gesammelt.
     *
     * Abstract methods of the class AbstractNormform have to be implemented in the derived class.
     *
     * @return bool true, if $errorMessages is empty, else false
     */
    protected function isValid(): bool
    {
        /*--
        require '../../onlineshopsolution/index/isValid.inc.php';
        //*/

        $this->currentView->setParameter(new GenericParameter("errorMessages", $this->errorMessages));

        return (count($this->errorMessages) === 0);
    }

    /**
     * Process the user input, sent with a POST request
     *
     * Das Suchfeld wird mit GET übergeben und daher nicht hier behandelt.
     * Die AddToCart - Buttons sind in ein POST-Formular verpackt nicht als Hyperlinks mit GET-Parametern.
     * Das ist abhängig vom Shopsystem, wie das in der Praxis gehandhabt wird. Es gibt POST und GET-Implementierungen.
     * Wir nehmen POST, weil wir die TNormformabläufe schon kennen, die auf POST ausgelegt sind.
     *
     * Über Shop::addToCart() wird das ausgewählte Produkt in den Warenkorb Tabelle onlineshop.cart gespeichert.
     * Im Gutfall wird in $this->statusMsg eine Rückmeldung gegeben, welches Produkt in den Warenkorb gelegt wurde.
     *
     * Abstract methods of the class AbstractNormform have to be implemented in the derived class.
     *
     * @throws DatabaseException is thrown by all methods of $this->dbAccess and not treated here.
     *         The exception is treated in the try-catch block of the php script, that initializes this class.
     */
    protected function business(): void
    {
        if (isset($_POST[self::PID])) {
            $this->addToCart();
            $this->statusMessage = "Product $this->pid added";
            $this->currentView->setParameter(new GenericParameter("statusMessage", $this->statusMessage));
            $this->currentView->setParameter(new GenericParameter("pageArray", $this->fillpageArray()));
            $this->currentView->setParameter(new PostParameter(OnlineShop::SEARCH, true));
        } else {
            $this->errorMessages ["addToCart"] = "Error adding Product to Cart. Please try again";
        }
    }

    /**
     * Befüllt das Array um alle Produkte aufzulisten, die auf der aktuellen Seite angezeigt werden.
     *
     * Es werden nur aktive Produkte berücksichtigt, bei denen die Spalte onlineshop.product.active='1' ist.
     *
     * Es werden nur die Produkte gelesen, die dem Suchkriterium entsprechen.
     * Das Suchkriterium aus dem Suchfeld (GET-Formular) kann leer sein.
     * Dann gibt es keine Einschränkung.
     * Suchfelder über die LIKE-Klausel sind die Spalten onlineshop.product_name, .short_description, long_description.
     * Der Suchbegriff wird in @see Shop::setSearch() ermittelt.
     *
     * Der Startwert der LIMIT-Klausel wird in @see Shop::setPaginationParameters() ermittelt.
     *
     * Die Datensätze werden in der mittels $_GET[self::SORT] geschickten Sortierreihenfolge ausgegeben (ORDER BY).
     * Die Sortierreihenfolge wird durch @see Shop::setOrderBy() ermittelt.
     *
     * Es werden so viele Sätze gelesen, wie in der Konstante DISPLAY festgelegt.
     *
     * @throws DatabaseException is thrown by all methods of $this->dbAccess and not treated here.
     *         The exception is treated in the try-catch block of the php script, that initializes this class.
     */
    private function fillpageArray()
    {
        $this->setSearch();
        $this->setPaginationParameters();
        $this->setOrderBy();
        $this->currentView->setParameter(new GenericParameter("pagecount", $this->pagecount));
        $this->currentView->setParameter(new GenericParameter("pagenumber", $this->pagenumber));
        $this->currentView->setParameter(new GenericParameter("current_page", $this->current_page));
        $this->currentView->setParameter(new GenericParameter("startprevious", $this->startprevious));
        $this->currentView->setParameter(new GenericParameter("startnext", $this->startnext));
        // TODO Umschreiben, dass das Array pageArray aus der Datenbank befüllt wird
        //##
        return $pageArray = array( 0 => array('idproduct' => 1,
                                              'product_name' => 'Passivhaus',
                                              'price' => 300000,00),
                                   1 => array('idproduct' => 2,
                                              'product_name' => 'Niedrigenergiehaus',
                                              'price' => 200000,00),
                                   2 => array('idproduct' => 3,
                                              'product_name' => 'Almgrundstück',
                                              'price' => 100000,00));
        //*/
        /*--
        require '../../onlineshopsolution/index/fillpageArray.inc.php';
        return $this->dbAccess->fetchResultset();
        //*/
    }

    /**
     * Hilfsmethode für die Suche in der Tabelle onlineshop.product
     *
     * Der Suchbegriff $this->search, der vom Benutzer im GET-Formular eingegeben wurde, für die LIKE-Klausel im Select
     * @see Shop::setPageCount()
     * wird ermittelt, validiert und im Session-Array zwischengespeichert,
     * damit er beim Blättern über Seitenaufrufe hinweg erhalten bleibt.
     *
     * Der Wert der vorherigen Suche wird in $this->oldsearch zwischengespeichert.
     * Der Startwert $this->start wird immer auf 0 gesetzt, wenn eine neue Suche gestartet wurde.
     * Erkannt wird das daran, dass in $this->oldsearch ein anderer Wert gespeichert ist als in $_GET[self::SEARCH]
     * @see Shop::setStart()
     *
     * Wurde der Suchbegriff mit $_GET mitgegeben wird er von dort übernommen und im Array $_SESSION hinterlegt.
     * Kommt er aus $_GET, wird mit Utilies::isSearchString() sichergestellt,
     * dass es sich um einen einzelnen Suchbegriff handelt.
     * Das macht Sinn, weil wir hier mit LIKE arbeiten und nicht mit einer Volltextsuche.
     * Ist er nicht in $_GET und bereits in $_SESSION wird er aus $_SESSION übernommen.
     * In allen anderen Fälle wird er auf NULL gesetzt.
     * Der reguläre Ausdruck in Utilities::isSingleWord() verhindert XSS.
     */
    private function setSearch()
    {
        if (isset($_SESSION[self::SEARCH])) {
            $this->oldsearch = $_SESSION[self::SEARCH];
        } else {
            $this->oldsearch = "";
        }
        if (isset($_POST[self::SEARCH]) && !Utilities::isSingleWord($_POST[self::SEARCH])) {
            $this->errorMessages[self::SEARCH] = "Please enter a single word. We use LIKE instead of FULLTEXT search";
        } elseif (isset($_POST[self::SEARCH]) && Utilities::isSingleWord($_POST[self::SEARCH])) {
            $_SESSION[self::SEARCH]=$_POST[self::SEARCH];
            $this->search=$_POST[self::SEARCH];
        } elseif (!isset($_POST[self::SEARCH]) && isset($_SESSION[self::SEARCH])) {
            $this->search=$_SESSION[self::SEARCH];
        } else {
            $this->search=null;
        }
    }

    /**
     * Hilfsmethode für das Blättern in der Produktliste
     *
     * Ermittelt über @see Shop::getPageCount(), die Anzahl der Seiten,
     * die zur Darstellung aller Datensätze notwendig sind.
     * Ermittelt den Startwert für alle Links der Blätterfunktion und
     * die LIMIT-Klausel im Select auf die Tabelle onlineshop.product
     * @see Shop::setStart(),
     * Außerdem werden die Seitenzahlen für alle Seiten ermittelt,
     * die durchblättert werden können und als Links mit dem Startwert im Template implementiert sind.
     * $this->pagenumber enthält für jede Seite $i deren Startwert. Die Seitenzahl $i ist der Key im Array.
     * Für die aktuelle Seite wird die Seitenzahl als Text in HTML ausgegeben
     * und nicht mit einem mit der Seitenzahl benannten Link hinterlegt.
     * @see templates/pagination.tpl
     */
    private function setPaginationParameters()
    {
        $this->setPageCount();
        $this->setStart();
        $this->current_page = ($this->start / self::DISPLAY) + 1;
        $this->startprevious = $this->start - self::DISPLAY;
        $this->startnext = $this->start + self::DISPLAY;
        for ($i = 1; $i <= $this->pagecount; $i++) {
            $this->pagenumber[$i] = (self::DISPLAY * ($i - 1));
        }
    }

    /**
     * Hilfsmethode für das Blättern in der Produktliste
     *
     * Ermittelt die Anzahl der Seiten $this->pagecount['count'], die notwendig ist um alle Produkte aufzulisten,
     * wenn nur soviele Produkte pro Seite angezeigt werden,
     * wie in der Konstante DISPLAY festgelegt ist. templates/pagination.html.twig
     *
     * Dazu muss im SELECT eine Gruppenfunktion zum Zählen genutzt werden und ein Alias, der count heißt (AS count).
     * Dann wird dieser Alias count im associativen Rückgabe-Array zum Key.
     * $this->rowcount['count'] wird in @see isValidStart() benötigt.
     * Durch die Verwendung einer Objekt-Variable $this->... ist sie dort automatisch sichtbar.
     *
     * Es werden nur aktive Produkte berücksichtigt, bei denen die Spalte onlineshop.product.active='1' ist.
     * Es werden nur Produkte gezählt, die dem Suchkriterium entsprechen.
     * Das Suchkriterium aus dem Suchfeld (GET-Formular) kann leer sein.
     * Dann gibt es keine Einschränkung.
     * Suchfelder über die LIKE-Klausel sind die Spalten
     * onlineshop.product.product_name, -.short_description, -.long_description.
     * Der Suchbegriff wird in @see Shop::setSearch() ermittelt.
     *
     * @throws DatabaseException Diese wird von allen $this->dbAccess Methoden geworfen und hier nicht behandelt.
     *         Die Exception wird daher nochmals weitergereicht (throw) und erst am Ende des Scripts behandelt.
     */
    private function setPageCount()
    {
        //##
        // Es sind 3 statische Datensätze
        // Damit man das Blättern sieht auf 2 gesetzt.
        // Auf beiden Seiten wird das gleiche angezeigt.
        // Erst durch DB-Zugriff wird auf jeder Seite das richtige angezeigt.
        $this->pagecount = 2;
        //*/
        /*--
        require '../../onlineshopsolution/index/setPageCount.inc.php';
        //*/
    }

    /**
     * Hilfsmethode für das Blättern in der Produktliste
     *
     * Der Startwert $this->start für die LIMIT-Klausel im Select
     * @see Shop::fillpageArray() für die aktuelle Seite wird ermittelt, validiert und im Session-Array gespeichert.
     * Wurde $_GET[self::START] mitgegeben, wird der Wert von dort übernommen
     * und es wird mit @see Utilies::isValidStart() sichergestellt,
     * dass es sich um eine positive Integerzahl handelt
     * und der Wert die Anzahl der Sätze in der Tabelle onlineshop.product nicht überschreitet.
     * Ist $_GET[self::START] nicht befüllt, aber $_SESSION[self::START] beinhaltet einen Wert
     * , wird er aus der Session übernommen.
     * In allen anderen Fällen wird er auf 0 gesetzt.
     *
     * Wurde ein neuer Suchbegriff eingegeben, wird der Startwert auf 0 gesetzt,
     * das heißt auf der ersten Seite der Produktliste gestartet.
     *
     * Tabelle onlinshop.product übersteigen, aber kein XSS. Die Startwerte werden in
     * @see src/basetemplates/pagination.tpl ausgegeben.
     */
    private function setStart()
    {
        if (isset($_GET[self::START]) && $this->isValidStart()) {
            $_SESSION[self::START]=$_GET[self::START];
            $this->start=$_GET[self::START];
        } elseif (!isset($_GET[self::START]) && isset($_SESSION[self::START])) {
            $this->start=$_SESSION[self::START];
        } else {
            $this->start=0;
        }
        if (isset($_SESSION[self::SEARCH]) &&
            isset($_POST[self::SEARCH]) &&
            $this->oldsearch !== $_POST[self::SEARCH]) {
            $_SESSION[self::START]=0;
            $this->start=0;
        }
    }

    /**
     * Validiert, ob ein Startwert für das Blättern gültig ist
     *
     * Mit @see Utilities::isInt() wird geprüft ob $_GET[self::START] einen positiven Integerwert enthält oder 0.
     * Dadurch wird XSS verhindert.
     * Zusätzlich wird geprüft, ob der Startwert kleiner ist als die Gesamtzahl der Sätze $this->rowcount['count']
     * in der Tabelle onlineshop.product, die in @see Shop::setPageCount() ermittelt wird.
     * Das verhindert Forced Browsing mit sinnlosen Werten.
     * Wäre der Startwert größer als die Anzahl der Sätze in onlineshop.product würde eine Leere Seite angezeigt.
     *
     */
    private function isValidStart()
    {
        if (Utilities::isInt($_GET[self::START]) && ($_GET[self::START] < $this->rowcount['count'])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Hilfsmethode für die Sortierung der Produktliste befüllt die Variable $order_by mit der ORDER BY-Klausel
     * für den Select in @see Shop::fillpageArray()
     *
     * Durch Shop:setSort() wird die Variable $this->sort befüllt.
     * Durch deren Inhalt wird die Spalte bestimmt, nach der die Datensätze der Tabelle onlineshop.product in
     * @see Shop::fillpageArray() sortiert werden.
     * Es wird nur die Sortierreihenfolge ASC unterstützt.
     * Durch die Implementierung einer PullDownBox mit mehreren Einträgen,
     * könnte man beliebige Sortierungen unterstützen.
     *
     * Dadurch diese Implementierung wird sowohl Forced Browsing, als auch XSS verhindert.
     */
    private function setOrderBy()
    {
        $this->setSort();
        switch ($this->sort) {
            case 'pid':
                $this->order_by = 'idproduct ASC';
                break;
            case 'pname':
                $this->order_by = 'product_name ASC';
                break;
            case 'price':
                $this->order_by = 'price ASC';
                break;
            default:
                $this->sort = 'pid';
                $this->order_by = 'idproduct ASC';
                break;
        }
    }

    /**
     * Hilfsmethode für die Sortierung der Produktliste
     *
     * Die Spalte $this->sort für die Sortierung in der ORDBY BY-Klausel im Select
     * @see Shop::fillpageArray() für die aktuelle Seite wird ermittelt, validiert und im Session-Array gespeichert.
     * Wurde sie mit $_GET[self::SORT] mitgegeben wird sie von dort übernommen und im Array $_SESSION hinterlegt,
     * um über Seitenaufrufe hinweg erhalten zu bleiben.
     * Kommt sie aus $_GET, wird mit @see Shop::isValidSort() sichergestellt,
     * dass es sich um eine gültige Sortierung handelt.
     * Ist sie nicht in $_GET und bereits in $_SESSION wird er aus $_SESSION übernommen.
     * In allen anderen Fälle wird sie auf 'pid' gesetzt.
     *
     * Dadurch wird sowohl forced Browsing, als auch XSS verhindert.
     */
    private function setSort()
    {
        if (isset($_GET[self::SORT]) && $this->isValidSort()) {
            $_SESSION[self::SORT]=$_GET[self::SORT];
            $this->sort=$_GET[self::SORT];
        } elseif (!isset($_GET[self::SORT]) && isset($_SESSION[self::SORT])) {
            $this->sort=$_SESSION[self::SORT];
        } else {
            $this->sort='pid';
        }
    }

    /**
     * Hilfsmethode für die Sortierung der Produktliste
     *
     * Diese Funktion filtert die übergebene Sortierung auf gültige Werte.
     *
     * Dadurch wird sowohl XSS verhindert, als auch ungewünschte Programmzustände durch Forced Browsing.
     *
     * @param mixed $sort Sortierwert, der aus $_GET kommt. Kann daher string oder number sein.
     * @return bool true, wenn der $sort in der Liste vorkommt. Anderfalls false.
     */
    private function isValidSort()
    {
        return in_array($_GET[self::SORT], array('pid','pname','price'));
    }

    /**
     * Prüft, ob eine pid im Array $_POST['pid'] in der Tabelle onlineshop.product vorkommt.
     *
     * Durch die Prüfung des Arrays mit der callback-Funktion array_map und Utilities::isInt wird verhindert,
     * dass durch Manipulation des POST-Requests völlig sinnlose Werte für die Datenbankanfrage verwendet werden können,
     * die ohnehin keinen Treffer ergeben könnten.
     * Es wird nur der erste Treffer im Array $_POST['pid'] für das Schreiben in die Datenbank verwendet.
     * An sich kann mit dem Drücken des Buttons nur ein Eintrag im Array $_POST['pid'] befüllt werden.
     * Jedoch sollen auch an dieser Stelle Manipulationen des POST-Requests unterbunden werden.
     * array_map() wird hier zu Demonstrationszwecken eingesetzt.
     * Ein direkter Aufruf von Utilities::isInt() in der foreach-Schleife würde auch genügen.
     * isValidPid() prüft mit Utilities::isInt() jeden KEY im Array $_POST['pid'][KEY]
     * auf positives Integer oder 0 um XSS zu verhindern.
     * Das ist nur einer (siehe vorher). Zusätzlich wird für diesen KEY geprüft,
     * ob dieser in der Tabelle onlineshop.product vorkommt, um Forced Browsing mit sinnlosen pids zu verhindern.
     *
     * @return bool false, wenn eine pid kein positives Integer ist,
     *                     oder nicht in der Datenbank vorkommt. Ansonsten true.
     * @throws DatabaseException Diese wird von allen $this->dbAccess Methoden geworfen und hier nicht behandelt.
     *         Die Exception wird daher nochmals weitergereicht (throw) und erst am Ende des Scripts behandelt.
     */
    private function isValidPid()
    {
        //##
        return true;
        //*/
        /*--
        require '../../onlineshopsolution/index/isValidPid.inc.php';
        if ($count['count'] === "1") {
            return true;
        } else {
            return false;
        }
        //*/
    }

    /**
     * Schreibt die Bestellung in den Warenkorb Tabelle onlineshop.cart
     *
     * Nur der erste Eintrag im Array wird in den Warenkorb gelegt.
     * Der Aufruf von break schadet nicht an dieser Stelle.
     * An sich ist durch den Aufruf des submit-Buttons sicher gestellt, dass es nur einen Eintrag gibt.
     * Allerdings werden dadurch Manipulationen des Requests mit mehreren Einträgen
     * im Array $_POST[self::PID] verhindert.
     *
     * @throws DatabaseException is thrown by all methods of $this->dbAccess and not treated here.
     *         The exception is treated in the try-catch block of the php script, that initializes this class.
     */
    private function addToCart()
    {
        //##
        return true;
        //*/
        /*--
        require '../../onlineshopsolution/index/addToCart.inc.php';
        //*/
    }
}

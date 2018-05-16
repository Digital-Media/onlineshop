<?php
namespace Exercises;

use Fhooe\NormForm\Core\AbstractNormForm;
use Fhooe\NormForm\Parameter\GenericParameter;
use Fhooe\NormForm\Parameter\PostParameter;
use Fhooe\NormForm\View\View;
use DBAccess\DBAccess;
use Utilities\Utilities;

/**
 * Die  Checkoutseite implementiert die finale Bestellung im OnlineShop.
 *
 * Die Checkout-Seite setzt auf der ojectorientieren Klasse TNormform und den Smarty-Templates auf.
 * Weiters benötigt es die Klasse DBAccess für Datenbankzugriffe, die die Klasse FileAccess ersetzt.
 * Durch die Verwendung von PDO Prepared Statements sind keine weiteren Maßnahmen gegen SQL-Injection notwendig.
 * XSS wird von der Klasse View verhindert für mit POST abgeschickte Eingabefelder
 *
 * In der Tabelle onlineshop.cart befinden sich Dummy-Einträge mit SessionID=1,
 * die für Testzwecke genutzt werden können, falls Ihre index.php nicht funktioniert.
 *
 * Beim Klicken des Button "Buy Now" wird die Bestellung abgeschickt.
 * Die Einträge aus der Tabelle onlineshop.cart werden in die Tabelle onlineshop.order_item übertragen
 * und in onlineshop.orders zu einer Bestellung zusammengefasst.
 * Im Erfolgsfall wird die über das Statusfeld "All products will be shipped" angezeigt und die Einträge
 * in der Tabelle onlineshop.cart gelöscht.
 * Eine Bestellung wird über den gleichen Eintrag in der Spalte onlineshop.cart.session_id zusammengefasst.
 *
 * Die Klasse ist final, da es keinen Sinn macht, davon noch weitere Klassen abzuleiten.
 *
 * @author Martin Harrer <martin.harrer@fh-hagenberg.at>
 * @package dab3
 * @version 2016
 */
final class Checkout extends AbstractNormForm
{
    // make trait Utilities accessible via $this->
    use Utilities;

    /**
     * @var string $dbAccess  Database handler for access to database
     */
    private $dbAccess;

    /**
     * Checkout constructor.
     *
     * Calls constructor of class AbstractNormForm.
     * Creates a database handler for the database connection.
     * The assigned constants can be found in src/defines.inc.php

     * @param View $defaultView Holds the initial @View object used for displaying the form.
     *
     * @throws DatabaseException is thrown by all methods of $this->dbAccess and not treated here.
     *         The exception is treated in the try-catch block of the php script, that initializes this class.
     */
    public function __construct(View $defaultView)
    {
        parent::__construct($defaultView);
        /*--
        require '../../onlineshopsolution/checkout/construct.inc.php';
        //*/
        // Add the images to our view since we can't do this from outside the object
        $this->currentView->setParameter(new GenericParameter("pageArray", $this->fillpageArray()));
    }

    /**
     * Validates the user input
     *
     * Abstracte Methode in der Klasse TNormform und muss daher hier implementiert werden
     * @see normform/TNormform.class.php
     *
     * @return bool immer true, weil es keine Eingabefehler gibt. Die Abläufe von TNormform werden trotzdem eingehalten,
     * weil es einen Submit-Button zur Bestätigung der
     * ausgegebenen Bestellung gibt, wobei im Folgenden als Verarbeitungschritt in die Datenbank geschrieben wird.
     */
    protected function isValid(): bool
    {
        $this->currentView->setParameter(new GenericParameter("errorMessages", $this->errorMessages));
        return (count($this->errorMessages) === 0);
    }

    /**
     * Process the user input, sent with a POST request
     *
     * Schreibt die Bestellungen aus dem Warenkorb Tabelle onlineshop.cart in die Bestelltabellen onlineshop.orders
     * und onlineshop.order_item
     * Weil die Bestellung auf zwei Tabellen aufgeteilt ist, muss das Schreiben der zwei Tabellen mit einer
     * Transaktionsklammer zusammengefasst werden.
     * Wenn das Schreiben beider Tabellen gut gegegangen ist, werden die Einträge der aktuellen Session aus der
     * Tabelle onlineshop.cart gelöscht.
     * In $this->statusMsg wird eine passende Status-Meldung, dass das Schreiben der Bestellung gut ging geschrieben.
     * Danach wird ein Commit abgesetzt.
     *
     * Weil es keine Überprüfungen während der Transaktion gibt, die ein Rollback erfordern könnten,
     * wird hier keines benötigt.
     * Im Falle einer Exception setzt die Datenbank die Transaktion automatisch zurück.
     *
     * Abstract methods of the class AbstractNormform have to be implemented in the derived class.
     *
     * @return mixed
     * @throws DatabaseException is thrown by all methods of $this->dbAccess and not treated here.
     *         The exception is treated in the try-catch block of the php script, that initializes this class.
     */
    protected function business(): void
    {
        /*--
        require '../../onlineshopsolution/checkout/business.inc.php';
        //*/
    }

    /**
     * Gibt ein Array mit allen Produkten im Warenkorb Tabelle onlineshop.cart,
     * die vom aktuell eingeloggten User bestellt wurden, zurück.
     *
     * Identifiziert wird der User hier über die session_id. Ältere Bestellungen,
     * die nicht abgeschickt wurden, werden in diesem Fall nicht berücksichtigt.
     * Dazu müsste die Tabelle onlineshop.cart auch eine Spalte user_id beinhalten.
     * Die Tabelle onlineshop.cart beinhaltet Dummyeinträge mit session_id=1.
     * Diese können für Testzwecke benutzt werden, falls Ihre index.php und mycart.php nicht funktionieren.
     *
     * Optional kann an das von der Datenbank zurückgegebene Resultset noch eine Zeile
     * mit einer Trennlinie <hr> und eine Zeile mit der Gesamtsumme angefügt werden.
     *
     * @return array|mixed $pageArray, wenn das Datensätze in der Tablle onlineshop.cart vorhanden sind.
     *                                 Ein leeres Array, wenn keine Einträge vorhanden sind. false im Fehlerfall.
     * @throws DatabaseException is thrown by all methods of $this->dbAccess and not treated here.
     *         The exception is treated in the try-catch block of the php script, that initializes this class.
     */
    protected function fillpageArray()
    {
        // TODO Umschreiben, dass das Array ptypeArray aus der Datenbank befüllt wird
        //##
        return $pageArray = array( 0 => array('product_idproduct' => 1,
                                       'product_name' => 'Passivhaus',
                                       'price' => 300000,00, 'quantity' => 1),
                            1 => array('product_idproduct' => 2,
                                       'product_name' => 'Niedrigenergiehaus',
                                       'price' => 200000,00, 'quantity' => 1),
                            2 => array('product_idproduct' => 3,
                                       'product_name' => 'Almgrundstück',
                                       'price' => 100000,00, 'quantity' => 1));
        //*/
        /*--
        require '../../onlineshopsolution/checkout/fillpageArray.inc.php';
        return $pageArray;
        //*/

    }

    /**
     * Ermittelt die Gesamtbestellsumme für die aktuelle session_id.
     *
     * Basis für die Berechnung ist die Tabelle onlineshop.cart
     *
     * @return mixed $result['totalsum'] Die Gesamtsumme der Bestellung
     * @throws DatabaseException is thrown by all methods of $this->dbAccess and not treated here.
     *         The exception is treated in the try-catch block of the php script, that initializes this class.
     */
    private function totalSum()
    {
        /*--
        require '../../onlineshopsolution/checkout/totalsum.inc.php';
        return $result['totalsum'];
        //*/
    }

    /**
     * Schreibt die Bestellung in die Tabelle onlineshop.orders.
     *
     * Die Spalte onlineshop.orders.iduser wird aus $_SESSION['iduser'] befüllt.
     * Die Spalte onlineshop.orders.total_sum wird durch ein Subselect aus der Tabelle onlineshop.cart gelesen.
     * Die Spalte onlineshop.orders.date_ordered wird mit now() mit dem aktuellen Zeitstempel befüllt.
     *
     *
     * Hier wird die Namenskonvention "Tabellennamen im Singular" gebrochen,
     * weil "order" in SQL ein reserviertes Wort ist.
     *
     * @return bool true, wenn der Eintrag erfolgreich geschrieben wurde.
     * @throws DatabaseException is thrown by all methods of $this->dbAccess and not treated here.
     *         The exception is treated in the try-catch block of the php script, that initializes this class.
     */

    private function addOrder()
    {
        /*--
        require '../../onlineshopsolution/checkout/addOrder.inc.php';
        //*/
    }

    /**
     * Schreibt für jede Zeile in der Tabelle onlineshop.cart mit der session_id der aktuellen Session
     * einen Eintrag in die Tabelle onlineshop.order_item
     *
     * Die Spalten onlineshop.order_item.idproduct, quantity, price werden mittels Subselect aus der
     * Tabelle onlineshop.cart gelesen.
     * Dies geschieht für jeden Eintrag, der in onlineshop.cart.sesssion_id die aktuelle SessionID beinhaltet.
     * Mit dem gleichen Select wird die Spalte onlineshop.order_item.idorder mit last_insert_id() ermittelt,
     * die die von Checkout::addOrder() erzeugt wurde.
     *
     * @return bool true, wenn die Einträge erfolgreich geschrieben wurden.
     * @throws DatabaseException is thrown by all methods of $this->dbAccess and not treated here.
     *         The exception is treated in the try-catch block of the php script, that initializes this class.
     */
    private function addItems()
    {
        /*--
        require '../../onlineshopsolution/checkout/addItems.inc.php';
        //*/
    }

    /**
     * Löscht alle Einträge mit der aktuellen SessionID in der Spalte onlineshop.cart.session_id
     * aus der Tabelle onlineshop.cart.
     *
     * @return bool
     * @throws DatabaseException is thrown by all methods of $this->dbAccess and not treated here.
     *         The exception is treated in the try-catch block of the php script, that initializes this class.
     */
    private function deleteWholeCart()
    {
        /*--
        require_once '../../onlineshopsolution/checkout/deleteWholeCart.inc.php';
        //*/
    }
}

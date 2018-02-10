<?php
/*
 * Das Warenkorbformular zeigt den Inhalte des Warenkorbs im OnlineShop an und läßt eine Änderung der Bestellmenge zu.
 *
 * Der Warenkorb setzt auf der ojectorientieren Klasse TNormform und den Smarty-Templatesauf.
 * Weiters benötigt es die Klasse DBAccess für Datenbankzugriffe, die die Klasse FileAccess ersetzt.
 * Durch die Verwendung von PDO Prepared Statements sind keine weiteren Maßnahmen gegen SQL-Injection notwendig
 * XSS wird von der Klasse View verhindert für mit POST abgeschickte Eingabefelder
 * In der Tabelle cart.onlineshop befinden sich Dummy-Einträge mit SessionID=1,
 * die für Testzwecke genutzt werden können, falls Ihre index.php nicht funktioniert.
 *
 * Der Warenkorb wird durch index.php gefüllt.
 * In mycart.php kann die Bestellmenge pro Produkt nochmals verwändert werden und
 *  in der Tabelle onlineshop.cart gespeichert werden (Buttoen Update Cart).
 *
 * Nach Änderung der Bestellmenge, kann auch gleich auf die Seite checkout.php weitergeleitet
 * werden (Button Go To Checkout). Die Änderungen werden auch in diesem Fall
 * in der Tabelle onlineshop.cart gespeichert
 *
 * Die Klasse ist final, da es keinen Sinn macht, davon noch weitere Klassen abzuleiten.
 *
 * @author Martin Harrer <martin.harrer@fh-hagenberg.at>
 * @package dab3
 * @version 2016
 */
final class MyCart extends AbstractNormForm
{
    /**
     *  Konstante für ein HTML Attribute <input name='quantity' id='quantity' ... >,
     * <label for='quantity' ... > --> $_POST[QUANTITY].
     */
    const QUANTITY = 'quantity';

    /**
     * @var string $dbAccess Datenbankhandler für den Datenbankzugriff
     */
    private $dbAccess;

    /**
     * MyCart Constructor.
     *
     * Ruft den Constructor der Klasse TNormform auf.
     * Erzeugt den Datenbankhandler mit der Datenbankverbindung
     * Die übergebenen Konstanten finden sich in src/defines.inc.php
     */
    public function __construct(View $defaultView, $templateDir = "templates", $compileDir = "templates_c")
    {
        parent::__construct($defaultView, $templateDir, $compileDir);
        /*--
        require '../onlineshopsolution/mycart/construct.inc.php';
        //*/
        // Add the images to our view since we can't do this from outside the object
        $this->currentView->setParameter(new GenericParameter("pageArray", $this->fillpageArray()));
    }

    /**
     * Validiert den Benutzerinput
     *
     * Das Array $_POST[self::QUANTITY] wird durchgegangen und jede pid mit Utilities::isInt() geprüft
     * Fehlermeldungen werden im Array $errorMessages[] gesammelt.
     * Für jede pid mit ungültiger quantity wird ein eigener Eintrag erstellt.
     * Optional kann hier die callback-Funktion array_map()
     * für die Prüfung des gesamten Arrays auf Utilites::isInt() verwendet werden.
     *
     * Abstracte Methode in der Klasse TNormform und muss daher hier implementiert werden
     *
     * @return bool true, wenn $errorMessages leer ist. Ansonsten false.
     */
    protected function isValid(): bool
    {
        /*--
        require '../onlineshopsolution/mycart/isValid.inc.php';
        //*/
        $this->currentView->setParameter(new GenericParameter("errorMessages", $this->errorMessages));
        return (count($this->errorMessages) === 0);
    }

    /**
     * Verarbeitet die Benutzereingaben, die mit POST geschickt wurden
     *
     * Schreibt über MyCart::changeCart() Änderungen der Quantity bei einem Produkt in die Tabelle onlineshop.cart.
     * Falls der Button "Change Cart" <input name='update' ... > gedrückt wurde,
     * wird im Erfolgsfall der Warenkorb nochmals angezeigt und in $statusMsg eine Nachricht geschrieben, dass die
     * Änderung der von onlineshop.cart.quantity erfolgreich war.
     * Wenn der Button "Go To Checkout" <inpurt name='checkout' ... > gedrückt wurde,
     * wird im Erfolgsfall auf die Seite checkout.php weitergeleitet.
     *
     * Abstracte Methode in der Klasse TNormform und muss daher hier implementiert werden
     *
     * @throws DatabaseException Diese wird von allen $this->dbAccess Methoden geworfen und hier nicht behandelt.
     *         Die Exception wird daher nochmals weitergereicht (throw) und erst am Ende des Scripts behandelt.
     */
    protected function business()
    {
        $this->changeCart();
        if (isset($_POST['update'])) {
            $this->currentView->setParameter(new GenericParameter("pageArray", $this->fillpageArray()));
            $this->statusMessage = "Quantity changed";
            $this->currentView->setParameter(new GenericParameter("statusMessage", $this->statusMessage));
        } elseif (isset($_POST['checkout'])) {
            View::redirectTo('checkout.php');
        }
    }

    /**
     * Gibt ein Array mit allen Produkten im Warenkorb Tabelle onlineshop.cart,
     * die vom aktuell eingeloggten User bestellt wurden.
     *
     * Identifiziert wird der User hier über die session_id. Ältere Bestellungen,
     * die nicht abgeschickt wurden, werden in diesem Fall nicht berücksichtigt.
     * Dazu müsste die Tabelle onlineshop.cart auch eine Spalte user_id beinhalten.
     * Die Tabelle onlineshop.cart beinhaltet Dummyeinträge mit session_id=1.
     * Diese können für Testzwecke benutzt werden, falls Ihre index.php und mycart.php
     * nicht funktionieren.
     *
     * @return array|mixed $pageArray mit allen Einträgen der Tabelle onlineshop.cart für die aktuelle Session.
     * Ein leeres Array, wenn keine Einträge vorhanden sind. false im Fehlerfall.
     * @throws DatabaseException Diese wird von allen $this->dbAccess Methoden geworfen und hier nicht behandelt.
     *         Die Exception wird daher nochmals weitergereicht (throw) und erst am Ende des Scripts behandelt.
     */
    private function fillpageArray()
    {
        // TODO Umschreiben, dass das Array ptypeArray aus der Datenbank befüllt wird
        //##
        return array( 0 => array('product_idproduct' => 1,
                                 'product_name' => 'Passivhaus',
                                 'price' => 300000,00, 'quantity' => 1),
                      1 => array('product_idproduct' => 2,
                                 'product_name' => 'Niedrigenergiehaus',
                                 'price' => 200000,00, 'quantity' => 1),
                      2 => array('product_idproduct' => 3,
                                 'product_name' => 'Almgrundstück',
                                 'price' => 100000,00,
                                 'quantity' => 1));
        //*/
        /*--
        require '../onlineshopsolution/mycart/fillpageArray.inc.php';
        $this->pageArray = $this->dbAccess->fetchResultset();
        return $this->pageArray;
        //*/
    }

    /**
     * Schreibt Änderungen aus $_POST['quantity']['pid'] in die Tabelle onlineshop.cart
     *
     * Falls für eine pid die quantity auf 0 gesetzt wurde, wird der Eintrag in der Tablle onlineshop.cart gelöscht
     * @see MyCart::deleteFromCart().
     * Falls für eine pid die quantity auf einen Wert ungleich 0 geändert wurde wird der Eintrag
     * der pid in der Tabelle onlineshop.cart geändert @see MyCart::updateCart().
     * Alle pids mit quantity = 0 werden ins Array $delete_array['pid'] geschrieben.
     * Alle anderen in das Array $update_array['pid'] und an MyCart::deleteFromCart() bzw.
     * MyCart::updateCart() übergeben.
     *
     * @throws DatabaseException wird von allen $this->dbAccess Methoden geworfen und hier nicht behandelt.
     *         Die Exception Diese wird daher nochmals weitergereicht (throw) und erst am Ende des Scripts behandelt.
     */
    private function changeCart()
    {
        /*--
        require '../onlineshopsolution/mycart/changeCart.inc.php';
        //*/
    }

    /**
     * Löst alle Einträge aus dem Array $delete_array aus der Tabelle onlineshop.cart für die aktuelle Session.
     *
     * @param array $delete_array  beinhaltet alle pids für Löschanforderungen für quantity=0.
     * @throws DatabaseException Diese wird von allen $this->dbAccess Methoden geworfen und hier nicht behandelt.
     *         Die Exception wird daher nochmals weitergereicht (throw) und erst am Ende des Scripts behandelt.
     */
    private function deleteFromCart($delete_array)
    {
        /*--
        require '../onlineshopsolution/mycart/deleteFromCart.inc.php';
        //*/
    }

    /**
     * Schreibt Änderungen für jede pid aus dem Array $update_array in die Spalte onlineshop.cart.quantity
     * für die aktuelle Session.
     * MyCart::updateCart() nutzt aus, dass prepared Statements pro prepare mehrfach ausgeführt werden können.
     *
     * @param array $update_array beinhaltet alle pids für Änderungsanforderungen für quantity!=0.
     * @throws DatabaseException Diese wird von allen $this->dbAccess Methoden geworfen und hier nicht behandelt.
     *         Die Exception wird daher nochmals weitergereicht (throw) und erst am Ende des Scripts behandelt.
     */
    private function updateCart($update_array)
    {
        /*--
        require '../onlineshopsolution/mycart/updateCart.inc.php';
        //*/
    }
}

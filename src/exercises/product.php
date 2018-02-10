<?php
/*
 * Das  Produkt-Formular ermöglicht es ein Produkt im OnlineShop anzulegen.
 *
 * Das Produkt-Formular setzt auf der ojectorientieren Klasse TNormform und den Smarty-Templates auf.
 * Weiters benötigt es die Klasse DBAccess für Datenbankzugriffe, die die Klasse FileAccess von IMAR ersetzt.
 * Im Erfolgsfall werden die Benutzerdaten in der Tabelle onlineshop.users gespeichert.
 * Durch die Verwendung von PDO Prepared Statements sind keine weiteren Maßnahmen gegen SQL-Injection notwendig
 * XSS wird von der Klasse View verhindert für mit POST abgeschickte Eingabefelder
 *
 * Die Klasse ist final, da es keinen Sinn macht, davon noch weitere Klassen abzuleiten.
 *
 * @author Martin Harrer <martin.harrer@fh-hagenberg.at>
 * @package dab3
 * @version 2016
 */
final class Product extends AbstractNormForm
{
    /**
     *  Konstanten für ein HTML Attribute <input name='pname' id='pname' ... >,
     * <label for='pname' ... > --> $_POST[PNAME].
     */
    const PNAME = "pname";
    const PTYPE = "ptype";
    const PRICE = "price";
    const ACTIVE = "active";
    const SHORTDESC = "shortdesc";
    const LONGDESC = "longdesc";

    /**
     * @var string $dbAccess Datenbankhandler für den Datenbankzugriff
     */
    private $dbAccess;

    /**
     * Product constructor.
     *
     * Ruft den Constructor der Klasse TNormform auf.
     * Erzeugt den Datenbankhandler mit der Datenbankverbindung
     * Die übergebenen Konstanten finden sich in src/defines.inc.php
     */
    public function __construct(View $defaultView, $templateDir = "templates", $compileDir = "templates_c")
    {
        parent::__construct($defaultView, $templateDir, $compileDir);
        /*--
        require '../onlineshopsolution/product/construct.inc.php';
        //*/
        $this->currentView->setParameter(new GenericParameter("ptypeArray", $this->autofillPTypeArray()));
    }

    /**
     * Validiert den Benutzerinput
     *
     * Alle Felder sind Pflichtfelder
     * pname wird gegen die Tabelle onlineshop.product geprüft, ob der Produktname eindeutig ist
     * @see Product::isUniquePName().
     * Der Preis wird mit Utilities::isPrice() validiert.
     * ptype wird gegen die Tabelle onlineshop.prdoduct_category geprüft, ob er darin vorhanden ist
     * @see Product::isValidPtype().
     * Fehlermeldungen werden im Array $errorMessages[] gesammelt.
     *
     * Abstracte Methode in der Klasse TNormform und muss daher hier implementiert werden
     *
     * @return bool true, wenn $errorMessages leer ist. Ansonsten false
     */
    protected function isValid(): bool
    {
        /*--
        require '../onlineshopsolution/product/isValid.inc.php';
        //*/
        $this->currentView->setParameter(
            new GenericParameter("selected", Utilities::sanitizeFilter($_POST[self::PTYPE]))
        );
        $this->currentView->setParameter(new GenericParameter("errorMessages", $this->errorMessages));
        return (count($this->errorMessages) === 0);
    }

    /**
     * Verarbeitet die Benutzereingaben, die mit POST geschickt wurden
     *
     * Ruft Product::addProduct(), um die validieren Benutzereingaben in die Tabelle onlineshop.product zu schreiben
     * Befüllt im Gutfall die Statusmeldung $this->statusMessage,
     * die Feedback über das erfolgreich angelegte Produkt gibt
     * und alle Daten nochmals auflistet außer short_description und long_description.
     * Abstracte Methode in der Klasse TNormform und muss daher hier implementiert werden
     *
     */
    protected function business()
    {
        $this->addProduct();
        /*--
        require '../onlineshopsolution/product/business.inc.php';
        //*/
        $this->currentView->setParameter(new GenericParameter("statusMessage", $this->statusMessage));
        $this->currentView->setParameter(new GenericParameter("ptypeArray", $this->autofillPTypeArray()));
        $this->currentView->setParameter(new GenericParameter("selected", null));
        $this->currentView->setParameter(new PostParameter(Product::PNAME, true));
        $this->currentView->setParameter(new PostParameter(Product::PRICE, true));
        $this->currentView->setParameter(new PostParameter(Product::PTYPE, true));
        $this->currentView->setParameter(new PostParameter(Product::ACTIVE, true));
        $this->currentView->setParameter(new PostParameter(Product::SHORTDESC, true));
        $this->currentView->setParameter(new PostParameter(Product::LONGDESC, true));
    }

    /**
     * Gibt alle Einträge der Tabelle onlineshop.product_category in einem Array zurück.
     *
     * @return mixed Array, das die Einträge der Tabelle onlineshop.product_category beinhaltet. false im Fehlerfall
     * @throws DatabaseException Diese wird von allen $this->dbAccess Methoden geworfen und hier nicht behandelt.
     *         Die Exception wird daher nochmals weitergereicht (throw) und erst am Ende des Scripts behandelt.
     */
    private function autofillPTypeArray()
    {
        // TODO Umschreiben, dass das Array aus der Datenbank befüllt wird
        //##
        return array( 0 => array('product_category_name' => 'Please Choose One'),
                      1 => array('product_category_name' => 'Fill with entries from database!'),
                      2 => array('product_category_name' => 'Yes, you should!'));
        //*/
        /*--
        require '../onlineshopsolution/product/autofillPTypeArray.inc.php';
        return $this->dbAccess->fetchResultset();
        //*/
    }

    /**
     * Prüft ob der im Array $_POST übergebene ptype in der Tabelle onlineshop.product_category vorhanden ist.
     *
     * @return bool true, wenn der ptype-Eintrag vorhanden ist. false, wenn nicht vorhanden.
     * @throws DatabaseException Diese wird von allen $this->dbAccess Methoden geworfen und hier nicht behandelt.
     *         Die Exception wird daher nochmals weitergereicht (throw) und erst am Ende des Scripts behandelt.
     */
    private function isValidPType()
    {
        //##
        return true;
        //*/
        /*--
        require '../onlineshopsolution/product/isValidPType.inc.php';
        if (count($rows) !== 0) {
            return true;
        } else {
            return false;
        }
        //*/
    }

    /**
     * Prüft ob pname in der Tabelle onlineshop.product bereits vorhanden ist.
     *
     * @return bool true, wenn pname in der Tabelle onlineshop.product nicht vorhanden ist. false,
     * wenn er bereits vorhanden ist.
     * @throws DatabaseException Diese wird von allen $this->dbAccess Methoden geworfen und hier nicht behandelt.
     *         Die Exception wird daher nochmals weitergereicht (throw) und erst am Ende des Scripts behandelt.
     */
    private function isUniquePName()
    {
        //##
        return true;
        //*/
        /*--
        require '../onlineshopsolution/product/isUniquePName.inc.php';
        if (count($rows) !== 0) {
            return false;
        } else {
            return true;
        }
        //*/
    }

    /**
     * Schreibt die validierten Benutzereingabe in die Tabelle onlineshop.product.
     *
     * @throws DatabaseException wird von allen $this->dbAccess Methoden geworfen und hier nicht behandelt.
     *         Die Exception Diese wird daher nochmals weitergereicht (throw) und erst am Ende des Scripts behandelt.
     */
    private function addProduct()
    {
        /*--
        require '../onlineshopsolution/product/addProduct.inc.php';
        $this->dbAccess->executeStmt($params);
        //*/
    }
}

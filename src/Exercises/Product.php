<?php
namespace Exercises;

use Fhooe\NormForm\Core\AbstractNormForm;
use Fhooe\NormForm\Parameter\GenericParameter;
use Fhooe\NormForm\Parameter\PostParameter;
use Fhooe\NormForm\View\View;
use DBAccess\DBAccess;
use Utilities\Utilities;

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
     * Constants for a HTML attribute in <input name='ptype' id='ptype' ... >, <label for='ptype' ... >
     * --> $_POST[self::PTYPE]
     */
    const PNAME = "pname";
    const PTYPE = "ptype";
    const PRICE = "price";
    const ACTIVE = "active";
    const SHORTDESC = "shortdesc";
    const LONGDESC = "longdesc";

    /**
     * @var string $dbAccess  Database handler for access to database
     */
    private $dbAccess;

    /**
     * Product constructor.
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
        require '../../onlineshopsolution/product/construct.inc.php';
        //*/
        $this->currentView->setParameter(new GenericParameter("ptypeArray", $this->autofillPTypeArray()));
    }

    /**
     * Validates the user input
     *
     * Alle Felder sind Pflichtfelder
     * pname wird gegen die Tabelle onlineshop.product geprüft, ob der Produktname eindeutig ist
     * @see Product::isUniquePName().
     * Der Preis wird mit Utilities::isPrice() validiert.
     * ptype wird gegen die Tabelle onlineshop.prdoduct_category geprüft, ob er darin vorhanden ist
     * @see Product::isValidPtype().
     * Fehlermeldungen werden im Array $errorMessages[] gesammelt.
     *
     * Abstract methods of the class AbstractNormform have to be implemented in the derived class.
     *
     * @return bool true, if $errorMessages is empty, else false
     */
    protected function isValid(): bool
    {
        /*--
        require '../../onlineshopsolution/product/isValid.inc.php';
        //*/
        $this->currentView->setParameter(
            new GenericParameter("selected", Utilities::sanitizeFilter($_POST[self::PTYPE]))
        );
        $this->currentView->setParameter(new GenericParameter("errorMessages", $this->errorMessages));
        return (count($this->errorMessages) === 0);
    }

    /**
     * Process the user input, sent with a POST request
     *
     * Ruft Product::addProduct(), um die validieren Benutzereingaben in die Tabelle onlineshop.product zu schreiben
     * Befüllt im Gutfall die Statusmeldung $this->statusMessage,
     * die Feedback über das erfolgreich angelegte Produkt gibt
     *
     * Abstract methods of the class AbstractNormform have to be implemented in the derived class.
     *
     */
    protected function business(): void
    {
        $this->addProduct();
        /*--
        require '../../onlineshopsolution/product/business.inc.php';
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
     * @throws DatabaseException is thrown by all methods of $this->dbAccess and not treated here.
     *         The exception is treated in the try-catch block of the php script, that initializes this class.
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
        require '../../onlineshopsolution/product/autofillPTypeArray.inc.php';
        return $this->dbAccess->fetchResultset();
        //*/
    }

    /**
     * Prüft ob der im Array $_POST übergebene ptype in der Tabelle onlineshop.product_category vorhanden ist.
     *
     * @return bool true, wenn der ptype-Eintrag vorhanden ist. false, wenn nicht vorhanden.
     * @throws DatabaseException is thrown by all methods of $this->dbAccess and not treated here.
     *         The exception is treated in the try-catch block of the php script, that initializes this class.
     */
    private function isValidPType()
    {
        //##
        return true;
        //*/
        /*--
        require '../../onlineshopsolution/product/isValidPType.inc.php';
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
     * @throws DatabaseException is thrown by all methods of $this->dbAccess and not treated here.
     *         The exception is treated in the try-catch block of the php script, that initializes this class.
     */
    private function isUniquePName()
    {
        //##
        return true;
        //*/
        /*--
        require '../../onlineshopsolution/product/isUniquePName.inc.php';
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
     * @throws DatabaseException is thrown by all methods of $this->dbAccess and not treated here.
     *         The exception is treated in the try-catch block of the php script, that initializes this class.
     */
    private function addProduct()
    {
        /*--
        require '../../onlineshopsolution/product/addProduct.inc.php';
        $this->dbAccess->executeStmt($params);
        //*/
    }
}

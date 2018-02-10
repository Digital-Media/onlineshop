<?php
namespace onlineshop\src;

use AbstractNormForm;
use onlineshop\src\DBAccess;
use GenericParameter;
use PostParameter;
use onlineshop\src\Utilities;
use View;

/**
 * Class DBDEmo implementiert eine Demoseite für die Normform zusammen mit der Datenbankklasse DBAccess des OnlineShop
 *
 * Die Seite dbdemo.php setzt auf der ojectorientieren Klasse AbstractNormForm und den Smarty-Templates auf.
 * Weiters benötigt es die Klasse DBAccess für Datenbankzugriffe, die die Klasse FileAccess ersetzt.
 * Durch die Verwendung von PDO Prepared Statements sind keine weiteren Maßnahmen gegen SQL-Injection notwendig.
 * XSS wird von der Klasse View verhindert für mit POST abgeschickte Eingabefelder
 *
 * Diese Seite listet die Produktkategorien des OnlineShops auf.
 * Über die Konstante DISPLAY (@see src/defines.inc.php) wird gesteuert,
 * wieviele Produkte pro Seite angezeigt werden.
 * Über DBDemo::addPType() wird die Produktkategorie in der Tabelle onlineshop.product_category angelegt.
 *
 * Die Klasse ist final, da es keinen Sinn macht, davon noch weitere Klassen abzuleiten.
 *
 * @author Martin Harrer <martin.harrer@fh-hagenberg.at>
 * @package onlineshop
 * @version 2016
 */
final class DBDemo extends AbstractNormForm
{
    // make trait Utilities accessible via $this->
    use Utilities;
    /**
     * Konstante für ein HTML Attribute in <input name='ptype' id='ptype' ... >, <label for='ptype' ... >
     * --> $_POST[self::PTYPE]
     */
    const PTYPE = 'ptype';

    /**
     * @var string $dbAccess Datenbankhandler für den Datenbankzugriff
     */
    private $dbAccess;

    /**
     * Shop Constructor.
     *
     * Ruft den Constructor der Klasse TNormform auf.
     * Erzeugt den Datenbankhandler mit der Datenbankverbindung
     * Die übergebenen Konstanten finden sich in src/defines.inc.php
     */
    public function __construct(View $defaultView, $templateDir = "templates", $compileDir = "templates_c")
    {
        parent::__construct($defaultView, $templateDir, $compileDir);
        $this->dbAccess = new DBAccess(DSN, DB_USER, DB_PWD, DB_NAMES, DB_COLLATION);
        $this->currentView->setParameter(new GenericParameter("pageArray", $this->fillpageArray()));
        // uncomment following lines to demonstrate error_handling
        // PHP Warning sichtbar im Browser bei display_errors=1
        //$x=1/0;
        // PHP Notice sichtbar im Browser bei display_errors=1
        //$this->hugo;
        // HTTP Status: 500 Page not Working, nur in /var/log/apache2/error.log zu sehen, bei log_errors=On
        //§this->hugo;
    }

    /**
     * Validiert den Benutzerinput
     *
     * Die Produktkategorie ptype wird gegen einen regulären Ausdruck geprüft,
     * der in Utilities::isSingleWord() festgelegt wird
     * Kann wegen use Utilities zu Beginn der Klassendeklaration auch mit $this->isSingleWord() aufgerufen werden.
     * Fehlermeldungen werden im Array $errorMessages[] gesammelt.
     *
     * Abstracte Methode in der Klasse TNormform und muss daher hier implementiert werden
     *
     * @return bool true, wenn $errorMessages leer ist. Ansonsten false
     */
    protected function isValid(): bool
    {
        if ($this->isEmptyPostField(self::PTYPE)) {
            $this->errorMessages[self::PTYPE] = "Please enter a Product Catecory.";
        }
        if (isset($_POST[self::PTYPE]) && !$this::isSingleWord($_POST[self::PTYPE])) {
            $this->errorMessages[self::PTYPE] = "Please enter a Product Category as a Single Word.";
        }
        $this->currentView->setParameter(new GenericParameter("errorMessages", $this->errorMessages));
        return (count($this->errorMessages) === 0);
    }

    /**
     * Verarbeitet die Benutzereingaben, die mit POST geschickt wurden
     *
     * Über Shop::addPType() wird die Produktkategorie in die Tabelle onlineshop.product_category gespeichert.
     * Im Gutfall wird in $this->statusMsg eine Rückmeldung gegeben, das die Schreiboperation erfolgreich war.
     *
     * Abstracte Methode in der Klasse TNormform und muss daher hier implementiert werden
     *
     * @throws DatabaseException wird von allen $this->dbAccess Methoden geworfen und hier nicht behandelt.
     *         Die Exception wird daher nochmals weitergereicht (throw) und erst am Ende des Scripts behandelt.
     */
    protected function business()
    {
        $this->addPType();
        $this->statusMessage = "Product Category added";
        $this->currentView->setParameter(new GenericParameter("statusMessage", $this->statusMessage));
        $this->currentView->setParameter(new GenericParameter("pageArray", $this->fillpageArray()));
        $this->currentView->setParameter(new PostParameter(DBDemo::PTYPE, true));
    }

    /**
     * Befüllt das Array um alle Produktkategorien aufzulisten, die auf der aktuellen Seite angezeigt werden.
     *
     * Es werden so viele Sätze gelesen, wie in der Konstante DISPLAY festgelegt. @see src/defines.inc.php
     *
     * @throws DatabaseException Diese wird von allen $this->dbAccess Methoden geworfen und hier nicht behandelt.
     *         Die Exception wird daher nochmals weitergereicht (throw) und erst am Ende des Scripts behandelt.
     */
    private function fillpageArray()
    {
        $query = <<<SQL
                 SELECT idproduct_category, product_category_name
                 FROM product_category
SQL;
        $this->dbAccess->prepareQuery($query, DEBUG);
        $this->dbAccess->executeStmt();
        return $this->dbAccess->fetchResultset();
    }


    /**
     * Schreibt die validierten Benutzereingabe in die Tabelle onlineshop.product_category.
     *
     * @throws DatabaseException wird von allen $this->dbAccess Methoden geworfen und hier nicht behandelt.
     *         Die Exception Diese wird daher nochmals weitergereicht (throw) und erst am Ende des Scripts behandelt.
     */
    private function addPType()
    {
        $query = <<<SQL
                 INSERT INTO product_category 
                 SET product_category_name = :ptype
SQL;
        $this->dbAccess->prepareQuery($query, DEBUG);
        // The next to lines do the same due to use Utilities at the begin of the class declaration
        //$params = array(':ptype' => Utilities::sanitizeFilter($_POST[self::PTYPE]));
        $params = array(':ptype' => $this->sanitizeFilter($_POST[self::PTYPE]));
        $this->dbAccess->executeStmt($params);
    }
}

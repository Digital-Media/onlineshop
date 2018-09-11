<?php
namespace DBAccess;

use Fhooe\NormForm\Core\AbstractNormForm;
use Fhooe\NormForm\Parameter\GenericParameter;
use Fhooe\NormForm\Parameter\PostParameter;
use Fhooe\NormForm\View\View;
use Utilities\Utilities;

/**
 * Class DBDEmo implements a demo page for the class AbstractNormForm combined with the class DBAccess of OnlineShop
 *
 * This class is initialized by htdocs/dbdemo.php, is derived from the class AbstractNormForm and uses TWIG templates.
 * Additionally the class DBAccess is used for database access.
 * DBAccess replaces the class FileAccess of the project PHPintro.
 * Due to the usage of PDO Prepared Statements no further steps are necessary to avoid SQL Injection in this use case.
 * XSS is prevented by the TWIG template engine, that escapes variables sent to a template automatically.
 *
 * This page lists the content of onlineshop.product_category and adds additional categories.
 *
 * Class DBAccess is final, because it makes no sense to derive a class from it.
 *
 * @author  Martin Harrer <martin.harrer@fh-hagenberg.at>
 * @package onlineshop
 * @version 2018
 */
final class DBAjaxDemo extends AbstractNormForm
{
    // make trait Utilities accessible via $this->
    use Utilities;
    /**
     * Constant for a HTML attribute in <input name='ptype' id='ptype' ... >, <label for='ptype' ... >
     * --> $_POST[self::PTYPE]
     */
    const PTYPE = 'ptype';

    /**
     * @var string $dbAccess  Database handler for access to database
     */
    private $dbAccess;

    /**
     * DBDemo Constructor.
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
        $this->dbAccess = new DBAccess(DSN, DB_USER, DB_PWD, DB_NAMES, DB_COLLATION);
        $this->currentView->setParameter(new GenericParameter("pageArray", $this->fillPageArray()));
        // uncomment following lines to demonstrate error_handling
        // PHP Warning is visible in browser with display_errors=1
        //$x=1/0;
        // PHP Notice visible in browser with display_errors=1
        //$this->hugo;
        // HTTP Status: 500 Page not Working, only visible in /var/log/apache2/error.log with log_errors=On
        //§this->hugo;
    }

    /**
     * Validates the user input
     *
     * The product category ptype is tested if it is empty.
     * Additionally it is validated with a regex given by Utilities::isSingleWord().
     * Due to "use Utilities" at the begin of this class, $this->isSingleWord() is also possible.
     * The trait is part of the current class then.
     * Error messages are written to the array $errorMessages[].
     *
     * Abstract methods of the class AbstractNormform have to be implemented in the derived class.
     *
     * @return bool true, if $errorMessages is empty, else false
     */
    protected function isValid(): bool
    {
        // Invalidate View for AJAX
        $this->currentView=null;
        if ($this->isEmptyPostField(self::PTYPE)) {
            $this->errorMessages[self::PTYPE] = "Please enter a Product Category.";
        }
        /*
        if (isset($_POST[self::PTYPE]) && !$this::isSingleWord($_POST[self::PTYPE])) {
            $this->errorMessages[self::PTYPE] = "Please enter a Product Category as a Single Word.";
        }
        */
        if ((count($this->errorMessages) !== 0)) {
            $this->errorMessages['errorMessages'] = count($this->errorMessages);
            $json = json_encode($this->errorMessages, JSON_UNESCAPED_SLASHES);
            echo $json;
        }
        return (count($this->errorMessages) === 0);
    }

    /**
     * Process the user input, sent with a POST request
     *
     * Shop::addPType() stores a new category in onlineshop.product_category.
     * If this works $this->statusMsg is set and displayed in the template.
     * All categories are read from onlineshop.product_category and displayed in the template.
     *
     * Abstract methods of the class AbstractNormform have to be implemented in the derived class.
     *
     * @throws DatabaseException is thrown by all methods of $this->dbAccess and not treated here.
     *         The exception is treated in the try-catch block of the php script, that initializes this class.
     */
    protected function business(): void
    {
        $this->addPType();
        echo  '{"statusMessage" : "Product Category added", "aid" : ' . $this->dbAccess->lastInsertId() . ' }';
    }

    /**
     * Returns an array to display all entries of onlineshop.product_category on the current page.
     *
     * @return array $result Result set of database query.
     * @throws DatabaseException is thrown by all methods of $this->dbAccess and not treated here.
     *         The exception is treated in the try-catch block of the php script, that initializes this class.
     */
    private function fillPageArray(): array
    {
        $query = <<<SQL
                 SELECT idproduct_category, product_category_name
                 FROM product_category
SQL;
        $this->dbAccess->prepareQuery($query, true);
        $this->dbAccess->executeStmt();
        $result = $this->dbAccess->fetchResultset();
        return $result;
    }


    /**
     * Writes validated user input to the table onlineshop.product_category.
     *
     * @throws DatabaseException is thrown by all methods of $this->dbAccess and not treated here.
     *         The exception is treated in the try-catch block of the php script, that initializes this class.
     */
    private function addPType(): void
    {
        $query = <<<SQL
                 INSERT INTO product_category 
                 SET product_category_name = :ptype
SQL;
        $this->dbAccess->prepareQuery($query, true);
        // The next two lines do the same due to "use Utilities" at the begin of the class declaration
        // $params = array(':ptype' => Utilities::sanitizeFilter($_POST[self::PTYPE]));
        //$params = array(':ptype' => $this->sanitizeFilter($_POST[self::PTYPE]));
        $params = array(':ptype' => $_POST[self::PTYPE]);
        $this->dbAccess->executeStmt($params);
    }
}

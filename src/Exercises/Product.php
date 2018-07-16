<?php
namespace Exercises;

use Fhooe\NormForm\Core\AbstractNormForm;
use Fhooe\NormForm\Parameter\GenericParameter;
use Fhooe\NormForm\Parameter\PostParameter;
use Fhooe\NormForm\View\View;
use DBAccess\DBAccess;
use Utilities\Utilities;

/*
 * The class Product stores product data in onlineshop.product.
 *
 * @author Martin Harrer <martin.harrer@fh-hagenberg.at>
 * @package OnlineShop
 * @version 2.0.2
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
     *
     * @param View $defaultView Holds the initial @View object used for displaying the form.
     *
     * @throws DatabaseException
     */
    public function __construct(View $defaultView)
    {
        parent::__construct($defaultView);
        /*--
        require '../../onlineshopsolution/product/construct.inc.php';
        //*/
        $this->currentView->setParameter(new GenericParameter("ptypeArray", $this->fillPTypeArray()));
    }

    /**
     * Validates the user input
     *
     * All fields are required.
     * pname is checked for uniqueness against onlineshop.product.product_name.
     * @see Product::isUniquePName().
     * Price can be validated with Utilities::isPrice().
     * ptype is checked against onlineshop.prdoduct_category, if it exists.
     * @see Product::isValidPtype().
     * Error messages are stored in the array $errorMessages[].
     *
     * @return bool true, if $errorMessages is empty, else false
     */
    protected function isValid(): bool
    {
        /*--
        require '../../onlineshopsolution/product/isValid.inc.php';
        //*/
        return (count($this->errorMessages) === 0);
    }

    /**
     * Process the user input, sent with a POST request
     *
     * Calls Product::addProduct(), to store the validated data in the table onlineshop.product.
     * On success $this->statusMessage is set and sent to the template (setParameter).
     */
    protected function business(): void
    {
        $this->addProduct();
        /*--
        require '../../onlineshopsolution/product/business.inc.php';
        //*/
    }

    /**
     * Returns all entries of the table onlineshop.product_category in an array.
     *
     * @return mixed Array, das die Einträge der Tabelle onlineshop.product_category beinhaltet. false im Fehlerfall
     * @throws DatabaseException
     */
    private function fillPTypeArray(): array
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
     * ptype in $_POST is checked against the table onlineshop.product_category, if it already exists.
     *
     * @return bool true, wenn der ptype-Eintrag vorhanden ist. false, wenn nicht vorhanden.
     * @throws DatabaseException
     */
    private function isValidPType(): bool
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
     * pname is checked for uniqueness against the table onlineshop.product.
     *
     * @return bool false, if pname in table onlineshop.product already exist, else true.
     * @throws DatabaseException
     */
    private function isUniquePName(): bool
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
     * Stores the product data in the table onlineshop.product.
     *
     * @throws DatabaseException
     */
    private function addProduct(): void
    {
        /*--
        require '../../onlineshopsolution/product/addProduct.inc.php';
        //*/
    }
}

<?php
namespace Exercises;

use Fhooe\NormForm\Core\AbstractNormForm;
use Fhooe\NormForm\Parameter\GenericParameter;
use Fhooe\NormForm\Parameter\PostParameter;
use Fhooe\NormForm\View\View;
use DBAccess\DBAccess;
use Utilities\Utilities;

/**
 * Class Shop implements the landing page (index.php) of OnlineShop
 *
 * The class Shop returns all products of the table onlineshop.product to page through.
 * How many entries are used per page is defined with the constant DISPLAY.
 * The search field uses a GET request to limit the result set.
 * The AddToCart button triggers the method Shop::addToCart() with a POST-Request to add a product to onlineshop.cart.
 *
 * @author Martin Harrer <martin.harrer@fh-hagenberg.at>
 * @package OnlineShop
 * @version 2.0.2
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
     * @var string START Key for $_GET and $_SESSION entry to define the starting entry of the displayed product list
     *                   @see src/basetemplates/pagination.html.twig
     * @var string SORT Key for $_GET- and $_SESSION entry, that defines the sort order
     * @var string SEARCH Key for $_POST entry for the search field
     * @var string PID Key for $_POST entry of the AddToCart button
     * @var string DISPLAY defines the number of entries displayed per page
     */
    const OFFSET = 'offset';
    const SORT = 'sort';
    const SEARCH = 'search';
    const PID = 'pid';
    const ROW_COUNT = 2;

    /**
     * @var string $dbAccess Database handler for access to database
     */
    private $dbAccess;

    /**
     * Shop constructor.
     *
     * Calls constructor of class AbstractNormForm.
     * Creates a database handler for the database connection.
     * Fills the pageArray and sends it to the template to list all products of onlineshop.product
     *
     * @param View $defaultView Holds the initial @View object used for displaying the form.
     *
     * @throws DatabaseException
     */
    public function __construct(View $defaultView)
    {
        parent::__construct($defaultView);
        /*-- 
        require '../../onlineshopsolution/index/construct.inc.php';
        //*/
        $this->currentView->setParameter(new GenericParameter("pageArray", $this->fillPageArray()));
    }

    /**
     * Validates the user input after a order request with one of the "AddToCart" buttons
     * or a search value was sent. Be aware, that the $_POST array either contains the search field or a pid.
     *
     * Even if the pids in the template are set by the PHP script index.php,
     * they have to be considered as user input,
     * because the data can be manipulated before they are sent to the server.
     * For each pid there is a separate button, therefore each pid has its own entry in an array $_POST['pid']
     * To validate each pid in the array $_POST['pid'] the method Shop::isValidPid() is called.
     * Error messages are stored in the array $errorMessages[].
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
     * The search field is sent with a GET request and not considered here, because it only affects the list.
     * The buttons "AddToCart" are enclosed by a for using the method POST instead of using hyperlinks with GET.
     * In practice it depends on the chosen shop system, which implementation is used for the order buttons.
     * We use POST, because AbstractNormForm is implemented for this case.
     *
     * Shop::addToCart() stores the chosen product in the table onlineshop.cart.
     * On success an appropriate message is set in $this->statusMessage and sent to the template(setParameter).
     *
     * @throws DatabaseException
     */
    protected function business(): void
    {
        if (isset($_POST[self::PID])) {
            $pid =$this->addToCart();
            $this->statusMessage = "Product $pid added";
            $this->currentView->setParameter(new GenericParameter("statusMessage", $this->statusMessage));
            $this->currentView->setParameter(new GenericParameter("pageArray", $this->fillPageArray()));
        } else {
            $this->errorMessages ["addToCart"] = "Error adding Product to Cart. Please try again";
        }
    }

    /**
     * Validates if the pid in $_POST['pid'] exists in onlineshop.product.
     *
     * Use Utilities::isInt to avoid requests to the database with values, that are not integer, which can't exist.
     *
     * $_POST['pid'] is an array, but only a array with one entry is valid, because each button AddToCart can
     * send only one pid. More entries in the array indicate, that someone manipulated the request.
     *
     * Each key in $_POST['pid'] is tested against onlineshop.product, if it exists, to avoid forced browsing.
     *
     * @return bool false, if pid is not a positiv integer or 0, or doesn't exist in the database.
     * @throws DatabaseException
     */
    private function isValidPid(): bool
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
     * Stores the order in onlineshop.cart
     *
     * $_POST['pid'] is an array, but only a array with one entry is valid, because each button AddToCart can
     * send only one pid. More entries in the array indicate, that someone manipulated the request.
     *
     * @return integer $pid idproduct of the product added to onlineshop.cart
     * @throws DatabaseException
     */
    private function addToCart(): int
    {
        //##
        return 0;
        //*/
        /*--
        require '../../onlineshopsolution/index/addToCart.inc.php';
        return $pid;
        //*/
    }

    /**
     * Returns an array with all products displayed on the current page.
     *
     * The offset for the LIMIT clause is set in @see Shop::setPaginationParameters().
     * The row count of the LIMIT clause is defined by the constant DISPLAY.
     *
     * The search value is set with @see Shop::setSearch().
     * The search criteria is not required (two independent POST forms).
     * The search fields for the LIKE clauses are onlineshop.product_name, -.short_description, -.long_description.
     *
     * The variable $order_by is set in @see Shop::setOrderBy().
     * The entries are sorted according to the value in $_GET[self::SORT] (ORDER BY).
     *
     * The result set contains only products
     *   with onlineshop.product.active='1'.
     *   that match the search value of the search field
     *
     * @return array result set of database query
     * @throws DatabaseException
     */
    private function fillPageArray(): array
    {
        $search = $this->setSearch();
        $order_by = $this->setOrderBy();
        $offset = $this->setPaginationParameters($search);
        $display = self::ROW_COUNT;

        // TODO Rewrite this code in way, that the array is filled with entries from the database
        // TODO For using LIMIT parameters you need to use DBAccess::bindValueByType()
        // TODO This is necessary, because offset and row_count of the LIMIT clause have to be integers (Syntax!!)
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
     *
     * @return string  holds the current value of the search field, if any given.
     * Detect if a new search value was sent and store it in $_SESSION for paging through the product list.
     *
     */
    private function setSearch(): string
    {
        if (isset($_POST[self::SEARCH])) {
            $_SESSION[self::SEARCH]=$_POST[self::SEARCH];
            return $_POST[self::SEARCH];
        } elseif (!isset($_POST[self::SEARCH]) && isset($_SESSION[self::SEARCH])) {
            return $_SESSION[self::SEARCH];
        } else {
            return "";
        }
    }

    /**
     * Defines how the result set should be sorted
     *
     * @var string $sort holds the current value for the ORDER BY clause of the product list displayed.
     * The GET array is filled with the sort value, when a user clicks a header field of the product list.
     *
     * The implementation avoids XSS and unwanted program states due to forced browsing.
     *
     * @return string sort order for order by clause
     */
    private function setOrderBy(): string
    {
        if (isset($_GET[self::SORT]) && in_array($_GET[self::SORT], array('pid','pname','price'))) {
            $_SESSION[self::SORT]=$_GET[self::SORT];
            $sort=$_GET[self::SORT];
        } elseif (!isset($_GET[self::SORT]) && isset($_SESSION[self::SORT])) {
            $sort=$_SESSION[self::SORT];
        } else {
            $sort='pid';
        }
        switch ($sort) {
            case 'pid':
                return 'idproduct ASC';
            case 'pname':
                return 'product_name ASC';
                break;
            case 'price':
                return 'price ASC';
                break;
            default:
               return 'idproduct ASC';
        }
    }

    /**
     * Set the parameters for paging through the product list
     *
     * @var string $product_count Number of products in onlineshop.produkt, that match the WHERE clause.
     *
     * @var string $page_count Number of pages needed to display the product list, depending on the value of DISPLAY
     *
     * To Avoid XSS and Forced Browsing, SQL-Injection ... the input is validated with @see Utilies::isValidStart()
     * and checked if it is less than $this->row_count['count'].
     *
     * @var string $offset_previous Start value for the LIMIT clause in the select statement for the "previous" link
     *
     * @var string $current_page Variable, that stores the page number of the current page of the product list
     *                           This value is not displayed as link, but as plain number in HTML
     *
     * @var string $offset_next Start value of the LIMIT clause of the select statement for the "next" link
     *
     * @var array $page_number Array with the start values of all pages,
     *                         for the links that have a page number $i assigned for direct access
     *
     * @see templates/pagination.tpl
     *
     * @return integer $offset offset for the LIMIT clause
     *                    If a pagination link is clicked $offset is set to $_GET[self::OFFSET]
     *                    The row_count for the LIMIT clause is defined by self::ROW_COUNT
     */
    private function setPaginationParameters($search): int
    {
        $page_number = [];
        $product_count = $this->setRowCount($search);
        //##
        // A static array with 3 entries is provided in fillPageArray()
        // $page_count is set to 2, to show the pagination links.
        // Both pages show the same 3 entries, because limiting the array to 2 entries works only
        // after selecting a result set from the database with a LIMIT clause.
        $page_count = 2;
        //*/
        //TODO calculate $page_count. How many pages are needed to display result set
        //TODO Only self::ROW_COUNT entries ard displayed on each page.
        /*--
        require '../../onlineshopsolution/index/setPaginationParameters.inc.php';
        //*/
        $this->currentView->setParameter(new GenericParameter("page_count", $page_count));

        if (isset($_GET[self::OFFSET]) && Utilities::isInt($_GET[self::OFFSET]) && ($_GET[self::OFFSET] < $product_count)) {
            $offset= (int) $_GET[self::OFFSET];
        }  else {
            $offset=0;
        }
        $offset_previous = $offset - self::ROW_COUNT;
        $this->currentView->setParameter(new GenericParameter("offset_previous", $offset_previous));

        $current_page = ($offset / self::ROW_COUNT) + 1;
        $this->currentView->setParameter(new GenericParameter("current_page", $current_page));

        $offset_next = $offset + self::ROW_COUNT;
        $this->currentView->setParameter(new GenericParameter("offset_next", $offset_next));

        for ($i = 1; $i <= $page_count; $i++) {
            $page_number[$i] = (self::ROW_COUNT * ($i - 1));
        }
        $this->currentView->setParameter(new GenericParameter("page_number", $page_number));

        return $offset;

    }

    /**
     * Calculate the number of entries in onlineshop.product.
     *
     * Consider using a group function in SQL to let the database count the rows.
     * You have to use an alias for this group operation to make this work in MariaDB
     *
     * Only active products are considered. onlineshop.product.active='1'
     * Only products, that match a given search string, are counted.
     * The search value form $_POST, can be empty. In this case no LIKE clause is used.
     * search fields for the LIKE clause are the columns
     * onlineshop.product.product_name, -.short_description, -.long_description.
     *
     * @param $search holds the search value
     * @return integer number of entries in onlineshop.product, that are active.
     * @throws DatabaseException
     */
    private function setRowCount($search): int
    {
        //##
        return $product_count = 3;
        //*/
        /*--
        require '../../onlineshopsolution/index/setRowCount.inc.php';
        return $product_count['count'];
        //*/
    }
}

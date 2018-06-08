<?php
namespace Exercises;

use Fhooe\NormForm\Core\AbstractNormForm;
use Fhooe\NormForm\Parameter\GenericParameter;
use Fhooe\NormForm\Parameter\PostParameter;
use Fhooe\NormForm\View\View;
use DBAccess\DBAccess;
use Utilities\Utilities;

/*
 * The form to display the cart shows the added products and enables a user to alter the quantity for each product.
 *
 * In case your index.php doesn't work,
 * you can create dummy entries in onlineshop.cart with SessionID=1 for testing purpose.
 * See /src/onlineshop.sql for a insert command to create some.
 *
 * The cart is filled with the AddToCart button on the page index.php.
 * With the button "Update Cart" on the page mycart.php a user can alter the quantity for each product and store it in
 * the table onlineshop.cart.
 *
 * After changing the quantity you can directly finalize the order by clicking the "Go To Checkout" Button
 * The changes are also saved in the table onlineshop.cart in this case.
 *
 * @author Martin Harrer <martin.harrer@fh-hagenberg.at>
 * @package OnlineShop
 * @version 2.0.2
 */
final class MyCart extends AbstractNormForm
{
    // make trait Utilities accessible via $this->
    use Utilities;
    /**
     * Constant for a HTML attribute in <input name='ptype' id='ptype' ... >, <label for='ptype' ... >
     * --> $_POST[self::PTYPE]
     */
    const QUANTITY = 'quantity';
    const PID = 'pid';

    /**
     * @var string $dbAccess  Database handler for access to database
     */
    private $dbAccess;

    /**
     * @var string $delete_array  array to store the pids to delete from onlineshop.cart to demonstrate
     *                            reuse of prepared statements. prepare once execute many times in deleteFromCart()
     */
    /*--
    private $delete_array;
    //*/
    /**
     * @var string $update_array  array to store the pids to update in onlineshop.cart to demonstrate
     *                            reuse of prepared statements. prepare once execute many times in updateCart()
     */
    /*--
    private $update_array;
    //*/

    /**
     * MyCart constructor.
     *
     * Calls constructor of class AbstractNormForm.
     * Creates a database handler for the database connection.
     * Fills the pageArray and sends it to the template
     * to list all orders in onlineshop.cart belonging to the current session
     *
     * @param View $defaultView Holds the initial @View object used for displaying the form.
     *
     * @throws DatabaseException
     */
    public function __construct(View $defaultView)
    {
        parent::__construct($defaultView);
        /*--
        require '../../onlineshopsolution/mycart/construct.inc.php';
        //*/
        $this->dbAccess = new DBAccess(DSN, DB_USER, DB_PWD, DB_NAMES, DB_COLLATION);
        $this->currentView->setParameter(new GenericParameter("pageArray", $this->fillpageArray()));
    }

    /**
     * Validates the user input sent via AJAX
     *
     * Error messages are stored in the array $errorMessages[].
     *
     * @return bool true, if $errorMessages is empty, else false
     */
    protected function isValid(): bool
    {
        $this->currentView=null;
        $error="";
        /*--
        require '../../onlineshopsolution/mycart/isValid.inc.php';
        //*/
        if (!(Utilities::isInt($_POST[self::QUANTITY])) || !(Utilities::isInt($_POST[self::PID]))) {
            $this->errorMessages[] = [self::QUANTITY => "Please enter a valid quantity for PID " . Utilities::sanitizeFilter($_POST[self::PID])];
        }
        if ((count($this->errorMessages) !== 0)) {
            $json = json_encode($this->errorMessages, JSON_UNESCAPED_SLASHES);
            echo '{"errorMessages": ' . $json . '}';
        }

        return (count($this->errorMessages) === 0);
    }

    /**
     * Process the user input, sent with a POST AJAX request
     *
     * Calls MyCart::changeCart() to store changes of a quantity to onlineshop.cart.
     *
     * @throws DatabaseException
     */
    protected function business(): void
    {
        if ((int) $_POST[self::QUANTITY] === 0) {
            $this->deleteFromCart();
            echo  '{"deleteMessage" : "Product with PID ' . Utilities::sanitizeFilter($_POST[self::PID]) . ' deleted from cart"}';
        } else {
            $this->updateCart();
            echo  '{"statusMessage" : "Quantity changed for PID ' . Utilities::sanitizeFilter($_POST[self::PID]) . '"}';
        }
    }

    /**
     * Returns an array with the orders in onlineshop.cart, that have been ordered bei user currently logged in.
     *
     * A user is identified with his current session_id. Orders of former sessions are not in the result set.
     * With a field onlineshop.cart.user_iduser former orders could be considered, if the user logged in before
     * leaving the site.
     *
     * @return array $pageArray, The result set, if valid entries exist in onlineshop.cart
     *                           An empty array, if no entries are there.
     * @throws DatabaseException
     */
    private function fillpageArray(): array
    {
        // TODO Rewrite this code in a way, that the array is filled with entries from the database
        /*##
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
        require '../../onlineshopsolution/mycart/fillpageArray.inc.php';
        $this->pageArray = $this->dbAccess->fetchResultset();
        return $this->pageArray;
        //*/
        $query = <<<SQL
                 SELECT product_idproduct, product_name, price, quantity
                 FROM cart
                 WHERE session_id = :session_id
SQL;
        $this->dbAccess->prepareQuery($query);
        //(session_id() != "") ? $sessionid = session_id() : $sessionid = 1;
        $this->dbAccess->executeStmt(array(':session_id' => 1));
        $this->pageArray = $this->dbAccess->fetchResultset();
        return $this->pageArray;
    }

    /**
     * Stores changes in $_POST['quantity']['pid'] to onlineshop.cart
     *
     * If quantity has been set to 0 for a pid, the entry is deleted in onlineshop.cart
     * You may use @see MyCart::deleteFromCart() for that.
     * If quantity has been set to a value not equal to 0 for a pid, the entry is updated in onlineshop.cart
     * You may use @see MyCart::updateCart() for that.
     * But you can also do both steps in MyCart::changeCart().
     *
     * @throws DatabaseException
     */
    private function changeCart(): void
    {
        /*--
        require '../../onlineshopsolution/mycart/changeCart.inc.php';
        //*/
    }

    /**
     * Deletes entries from onlineshop.cart belonging to the current session.
     *
     * @throws DatabaseException
     */
    private function deleteFromCart(): void
    {
        /*--
        require '../../onlineshopsolution/mycart/deleteFromCart.inc.php';
        //*/
        $query = <<<SQL
                 DELETE FROM  cart 
                 WHERE product_idproduct = :idproduct
                 AND session_id = :session_id
SQL;
        $this->dbAccess->prepareQuery($query);
        $pid = $_POST[self::PID];
        //(session_id() != "") ? $sessionid = session_id() : $sessionid = 1;
        $params = array(':idproduct' => $pid, ':session_id' => 1);
        $this->dbAccess->executeStmt($params);
    }

    /**
     * Updates onlineshop.cart.quantity for given pids belonging to the current session
     *
     * @throws DatabaseException
     */
    private function updateCart(): void
    {
        /*--
        require '../../onlineshopsolution/mycart/updateCart.inc.php';
        //*/
        $query = <<<SQL
                 UPDATE  cart 
                 SET quantity = :quantity
                 WHERE product_idproduct = :idproduct
                 AND session_id = :session_id
SQL;
        $this->dbAccess->prepareQuery($query);
        $pid = $_POST[self::PID];
        $quantity = $_POST[self::QUANTITY];
        //(session_id() != "") ? $sessionid = session_id() : $sessionid = 1;
        $params = array(':idproduct' => $pid, ':session_id' => 1, ':quantity' => $quantity);
        $this->dbAccess->executeStmt($params);
    }
}

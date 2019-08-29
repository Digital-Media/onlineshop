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

    //%%mycart/constants


    /**
     * @var object $dbAccess  Database handler for access to database
     */
    private $dbAccess;
    
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
        //%%mycart/construct
        $this->currentView->setParameter(new GenericParameter("pageArray", $this->fillpageArray()));
    }

    /**
     * Validates the user input
     *
     * Error messages are stored in the array $errorMessages[].
     *
     * @return bool true, if $errorMessages is empty, else false
     */
    protected function isValid(): bool
    {
        //%%mycart/isValid
        return (count($this->errorMessages) === 0);
    }

    /**
     * Process the user input, sent with a POST request
     *
     * Calls MyCart::deleteFromCart() and MyCart::updateCart() to store changes of a quantity to onlineshop.cart.
     * If the button "Go To Checkout" is clicked, the user is redirected to checkout.php (@see View::redirectTo())
     *
     * @throws DatabaseException
     */
    protected function business(): void
    {
        //%%mycart/business
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
        //##%%
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
        //#%#%
        //%%mycart/fillpageArray
    }

    /**
     * Validates if the pid in $_POST['pid'] exists in onlineshop.product.
     *
     * Each key in $_POST['pid'] is tested against onlineshop.product, if it exists, to avoid forced browsing.
     *
     * @param string $pid onlineshop.product.idproduct, that has to exist and to be active
     *
     * @return bool false, if pid is not a positiv integer or 0, or doesn't exist in the database.
     * @throws DatabaseException
     */
    private function isValidPid($pid): bool
    {
        //##%%
        return true;
        //#%#%
        //%%mycart/isValidPid
    }

    /**
     * Deletes entries from onlineshop.cart belonging to the current session.
     *
     * @param string $pid       pid of rows to be deleted from onlineshop.cart
     * @param string $sessionid current sessionid of rows to be deleted from onlineshop.cart
     *
     * @throws DatabaseException
     */
    private function deleteFromCart($pid, $sessionid): void
    {
        //%%mycart/deleteFromCart
    }

    /**
     * Updates onlineshop.cart.quantity for given pids belonging to the current session
     *
     * @param string $pid       pid of rows to be updated in onlineshop.cart
     * @param string $quantity  new quantity to be set in onlineshop.cart for given pid and sessionid
     * @param string $sessionid current sessionid of rows to be updated in onlineshop.cart
     *
     * @throws DatabaseException
     */
    private function updateCart($pid, $quantity, $sessionid): void
    {
        //%%mycart/updateCart
    }
}

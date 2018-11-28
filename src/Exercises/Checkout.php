<?php
namespace Exercises;

use Fhooe\NormForm\Core\AbstractNormForm;
use Fhooe\NormForm\Parameter\GenericParameter;
use Fhooe\NormForm\Parameter\PostParameter;
use Fhooe\NormForm\View\View;
use DBAccess\DBAccess;
use Utilities\Utilities;

/**
 * The Class Checkout implements the final order.
 *
 * In case your index.php doesn't work,
 * you can create dummy entries in onlineshop.cart with SessionID=1 for testing purpose.
 * See /src/onlineshop.sql for a insert command to create some.
 *
 * With the "Buy Now" button a order is finalized.
 * The entries in the table onlineshop.cart, that belong to the current session, are transfered to onlineshop.order_item.
 * An entry, that stands for the whole order, is created in onlineshop.orders.
 * One order in onlineshop.cart is connected via equal entries in the field onlineshop.cart.session_id.
 * On success a status message is set and all entries in onlineshop.cart are deleted.
 *
 * @author  Martin Harrer <martin.harrer@fh-hagenberg.at>
 * @package OnlineShop
 * @version 2.0.2
 */
final class Checkout extends AbstractNormForm
{
    // make trait Utilities accessible via $this->
    use Utilities;

    /**
     * @var object $dbAccess  Database handler for access to database
     */
    private $dbAccess;

    /**
     * Checkout constructor.
     *
     * Calls constructor of class AbstractNormForm.
     * Creates a database handler for the database connection.
     * Fills the pageArray sent to the template to display the current orders of onlineshop.cart.

     * @param View $defaultView Holds the initial @View object used for displaying the form.
     *
     * @throws DatabaseException
     */
    public function __construct(View $defaultView)
    {
        parent::__construct($defaultView);
        //%%checkout/construct
        $this->currentView->setParameter(new GenericParameter("pageArray", $this->fillpageArray()));
    }

    /**
     * Validates the user input
     *
     * The data shown in the browser are are read from onlineshop.cart and not sent back after submit.
     * The submit only triggers the transfer of the data from onlineshop.cart to onlineshop.order_items
     * and onlineshop.orders in the method business().
     *
     * @return bool always true, because there are no input fields.
     */
    protected function isValid(): bool
    {
        // Invalidate View for AJAX
        // $this->currentView=null;
        return (count($this->errorMessages) === 0);
    }

    /**
     * The data from onlineshop.cart are transfered to onlineshop.orders and onlineshop.order_item
     *
     * Because the order is split up into two tables, the inserts have to be enclose by "begin transaction" and "commit".
     * If both inserts succeed, the order is deleted from onlineshop.cart.
     * The delete also has to be within "begin transaction" and "commit".
     * After the commit a statusMessage is set to an appropriate value, stating that the order was successful.
     *
     * During the transaction there are no validation, that can cause an explicit rollback.
     * In case of an exception the database automatically rolls back.
     * Therefore only a commit is needed.
     *
     * @return mixed
     * @throws DatabaseException
     */
    protected function business(): void
    {
        //%%checkout/business
    }

    /**
     * Returns an array with all products stored in onlineshop.cart, that have been ordered by a logged in user
     * in the current session.
     *
     * A user is identified with his current session_id. Orders of former sessions are not in the result set.
     * With a field onlineshop.cart.user_iduser former orders could be considered, if the user logged in before
     * leaving the site.
     *
     * Adds a line to $pageArray, that holds total_sum.
     *
     * @return array $pageArray, The result set, if valid entries exist in onlineshop.cart
     *                           An empty array, if no entries are there.
     * @throws DatabaseException
     */
    protected function fillpageArray(): array
    {
        // TODO rewrite this code in a way, that the entries are filled from the database.
        //##%%
        return $pageArray = array(
                                0 => array('product_idproduct' => 1,
                                           'product_name' => 'Passivhaus',
                                           'price' => 300000,00, 'quantity' => 1),
                                1 => array('product_idproduct' => 2,
                                           'product_name' => 'Niedrigenergiehaus',
                                           'price' => 200000,00, 'quantity' => 1),
                                2 => array('product_idproduct' => 3,
                                           'product_name' => 'Almgrundstück',
                                           'price' => 100000,00, 'quantity' => 1)
                            );
        //#%#%
        //%%checkout/fillpageArray
    }

    /**
     * Calculates the total sum for the current session_id from onlineshop.cart.
     *
     * @return array $result['totalsum']
     * @throws DatabaseException
     */
    private function totalSum(): string
    {
        //%%checkout/totalsum
    }

    /**
     * Stores the order in the table onlineshop.orders.
     *
     * The column onlineshop.orders.iduser is set to $_SESSION['iduser'].
     * The column onlineshop.orders.total_sum is built with a subselect on the table onlineshop.cart.
     * The column onlineshop.orders.date_ordered is set to now().
     *
     * @throws DatabaseException
     */

    private function addOrder(): void
    {
        //%%checkout/addOrder
    }

    /**
     * All entries in the table onlineshop.cart belonging to the current session are transfered to
     * onlineshop.order_item
     *
     * The columns onlineshop.order_item.idproduct, quantity, price are read from the table
     * onlineshop.cart via subselect. Additionally this subselect determines the PK used for Checkout::addOrder() to
     * use it in onlineshop.order_item.orders_idorders as FK. --> last_insert_id()
     * The subselect gathers only entries, where the content of onlineshop.cart.session_id is
     * equivalent to the current session_id.
     *
     * @throws DatabaseException
     */
    private function addItems(): void
    {
        //%%checkout/addItems
    }

    /**
     * The entries in onlineshop.cart belonging to the current session are deleted.
     *
     * @throws DatabaseException
     */
    private function deleteWholeCart(): void
    {
        //%%checkout/deleteWholeCart
    }
}

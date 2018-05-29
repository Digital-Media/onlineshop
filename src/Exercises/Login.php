<?php
namespace Exercises;

use Fhooe\NormForm\Core\AbstractNormForm;
use Fhooe\NormForm\Parameter\GenericParameter;
use Fhooe\NormForm\Parameter\PostParameter;
use Fhooe\NormForm\View\View;
use DBAccess\DBAccess;
use Utilities\Utilities;

/**
 * The class Login implements the login to OnlineShop.
 *
 * On success the variable $_SESSION[LOGGEDIN] is filled with a special hash.
 * User credentials are validated against the table onlineshop.user
 *
 * @author  Martin Harrer <martin.harrer@fh-hagenberg.at>
 * @package OnlineShop
 * @version 2.0.2
 */
final class Login extends AbstractNormForm
{
    // make trait Utilities accessible via $this->
    use Utilities;
    /**
     * Constants for a HTML attribute in <input name='ptype' id='ptype' ... >, <label for='ptype' ... >
     * --> $_POST[self::PTYPE]
     */
    const EMAIL = "email";
    const PASSWORD = "password";

    /**
     * @var string $dbAccess  Database handler for access to database
     */
    private $dbAccess;

    /**
     * Login constructor.
     *
     * Calls constructor of class AbstractNormForm.
     * Creates a database handler for the database connection.
     * The assigned constants can be found in src/defines.inc.php
     *
     * @param View $defaultView Holds the initial @View object used for displaying the form.
     *
     * @throws DatabaseException
     */
    public function __construct(View $defaultView)
    {
        parent::__construct($defaultView);
        //--
        require '../../onlineshopsolution/login/construct.inc.php';
        //*/
    }

    /**
     * Validates the user input
     *
     * email and password are required fields.
     * The combination of email + password is checked against database in @see Login::authenitcateUser()
     * Error messages are stored in the array $errorMessages[].
     *
     * @return bool true, if $errorMessages is empty, else false
     */
    protected function isValid(): bool
    {
        //--
        require '../../onlineshopsolution/login/isValid.inc.php';
        //*/
        /*##
        $this->authenticateUser();
        //*/
        $this->currentView->setParameter(new GenericParameter("errorMessages", $this->errorMessages));
        return (count($this->errorMessages) === 0);
    }

    /**
     * Process the user input, sent with a POST request
     *
     * If a user called a page, that is protected by login, he will be redirected back to the page he requested.
     * If he directly requested the login page he is redirected to index.php
     * A page protected by login has to store its name in $_SESSION['redirect'] to make this redirect possible.
     */
    protected function business(): void
    {
        isset($_SESSION[REDIRECT]) ? $redirect= $_SESSION['redirect'] : $redirect='index.php';
        View::redirectTo($redirect);
    }

    /**
     * Validates email and password against onlineshop.user
     *
     * After a successful login the session_id is regenerated to make session hijacking more difficult.
     * session_regenerate_id() is used for that.
     * After that the session_ids in onlineshop.cart have to be replaced with the new one.
     *
     * @return bool true, if email+password match a row in onlineshop.user, else false.
     * @throws DatabaseException
     */
    private function authenticateUser(): bool
    {
        /*##
        $_SESSION['iduser']=1;
        $_SESSION[IS_LOGGED_IN] = Utilities::generateLoginHash();
        $_SESSION['first_name']='John';
        $_SESSION['last_name']='Doe';
        return true;
        //*/
        // copy solution from onlineshopsolution/login/authenticateUser.inc.php here to make solution work.
        // require doesn't work in this case
        //
        $query = <<<SQL
                 SELECT iduser, first_name, last_name, password 
                 FROM user 
                 WHERE email=:email 
                 AND active IS NULL
SQL;
        $this->dbAccess->prepareQuery($query);
        $this->dbAccess->executeStmt(array(':email' => $_POST[self::EMAIL]));
        $rows = $this->dbAccess->fetchResultset();
        if (count($rows) === 1 && password_verify($_POST[self::PASSWORD], $rows[0]['password'])) {
            // TODO Optional, Warenkorb über session_regenerate_id() hinweg erhalten
            $old_session_id = session_id();
            session_regenerate_id();
            $this->updateCart($old_session_id, session_id());
            // End optional
            $_SESSION['iduser']=$rows[0]['iduser'];
            $_SESSION[IS_LOGGED_IN] = Utilities::generateLoginHash();
            $_SESSION['first_name']=$rows[0]['first_name'];
            $_SESSION['last_name']=$rows[0]['last_name'];
            return true;
        } else {
            return false;
        }
    }

    /**
     * Replaces the session_ids in onlineshop.cart after the session has been regenerated after a successful login.
     *
     * @return bool true, wenn das update gut gegangen ist. false, wenn das nicht der Fall ist.
     * @throws DatabaseException
     */
    private function updateCart($old_session_id, $new_session_id)
    {
        /*##
        return true;
        //*/
        //--
        require '../../onlineshopsolution/login/updateCart.inc.php';
        //*/
    }
}

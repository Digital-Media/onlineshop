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
 * @version 5.0.2
 */
final class Login extends AbstractNormForm
{
    use Utilities;

    /**
     * Constants for a HTML attribute in <input name='ptype' id='ptype' ... >, <label for='ptype' ... >
     * --> $_POST[self::PTYPE]
     */
    const EMAIL = "email";
    const PASSWORD = "password";

    /**
     * @var object $dbAccess  Database handler for access to database
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
        //%%login/construct
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
        //%%login/isValid
        //##%%
        $this->authenticateUser();
        //#%#%
        return (count($this->errorMessages) === 0);
    }

    /**
     * Process the user input, sent with a POST request
     *
     * If a user called a page, that is protected by login, he will be redirected back to the page he requested.
     * If he directly requested the login page he is redirected to index.php
     * A page protected by login has to store its name in $_SESSION['redirect'] to make this redirect possible.
     *
     * @see View::redirect() for this.
     */
    protected function business(): void
    {
        isset($_SESSION[REDIRECT]) ? $redirect = $_SESSION['redirect'] : $redirect = 'index.php';
        // replace the following line with a solution for AJAX, when using AJAX
        View::redirectTo($redirect);
    }

    /**
     * Validates email and password against onlineshop.user
     *
     * After a successful login the session_id is regenerated to make session hijacking more difficult.
     * session_regenerate_id() is used for that.
     * After that the corresponding session_ids in onlineshop.cart have to be replaced with the new one.
     *
     * In the table onlineshop.user the BCRYPT algorithm ist used for hashing onlineshop.user.password.
     * This was done in PHP 5.6 with password_hash(... , PASSWORD_DEFAULT)
     *
     * With PHP 7.3 the challenge is to update older hashes to the strongest hash, that is currently available.
     * Therefore password_get_info(), password_verify() and password_needs_rehash() are used to store
     * an argon2 hash in onlineshop.user.password, after a successful login against the old password hash.
     *
     * @return bool true, if email+password match a row in onlineshop.user, else false.
     * @throws DatabaseException
     */
    private function authenticateUser(): bool
    {
        //TODO use $old_session_id=1 for testing purpose as provided in onlineshop.cart
        //TODO when the whole shop works, you can switch to session_id()
        //##%%
        $_SESSION['iduser'] = 1;
        $_SESSION[IS_LOGGED_IN] = Utilities::generateLoginHash();
        $_SESSION['first_name'] = 'John';
        $_SESSION['last_name'] = 'Doe';
        return true;
        //#%#%
        //%%login/authenticateUser
    }

    /**
     * Replaces the session_ids in onlineshop.cart after the session has been regenerated after a successful login.
     *
     * @return bool true, if update succeeds - false, if it fails.
     * @throws DatabaseException
     */
    private function updateCart($old_session_id, $new_session_id)
    {
        //##%%
        return true;
        //#%#%
        //%%login/updateCart
    }
        //%%login/updateUser
}

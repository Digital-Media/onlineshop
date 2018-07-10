<?php
namespace Exercises;

use Fhooe\NormForm\Core\AbstractNormForm;
use Fhooe\NormForm\Parameter\GenericParameter;
use Fhooe\NormForm\Parameter\PostParameter;
use Fhooe\NormForm\View\View;
use DBAccess\DBAccess;
use Utilities\Utilities;

/*
 * The class Register implements a registration of a user at onlineshop.
 *
 * If user credentials are valid, they are stored in the table onlineshop.user.
 *
 * @author Martin Harrer <martin.harrer@fh-hagenberg.at>
 * @package OnlineShop
 * @version 2.0.2
 */
final class Register extends AbstractNormForm
{
    /**
     * Constants for a HTML attribute in <input name='ptype' id='ptype' ... >, <label for='ptype' ... >
     * --> $_POST[self::PTYPE]
     */
    const FIRSTNAME = "firstname";
    const LASTNAME = "lastname";
    const NICKNAME = "nickname";
    const PHONE = "phone";
    const MOBILE = "mobile";
    const FAX = "fax";
    const EMAIL = "email";
    const PASSWORD = "password";
    const PASSWORD_REPEAT = "password_repeat";

    /**
     * @var string $dbAccess  Database handler for access to database
     */
    private $dbAccess;

    /**
     * Register Constructor.
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
        require '../../onlineshopsolution/register/construct.inc.php';
        //*/
    }

    /**
     * Validates the user input
     *
     * email is validated with a regex. You can use Utilities::isEmail() to do so.
     * Additionally email is checked for uniqueness against onlineshop.user.
     * password is validated with a regex. You can use Utilitie::isPassword() to do so.
     * At least one of the fields phone, mobile and fax is required.
     * If filled they are checked with a regex. You can use Utilities::isPhone() to do so.
     * All other fields are required. You can use AbstractNormform::isEmptyPostField() for validation.
     * Error messages are strored in the array $errorMessages[].
     *
     * Abstract methods of the class AbstractNormform have to be implemented in the derived class.
     *
     * @return bool true, if $errorMessages is empty, else false
     */
    protected function isValid(): bool
    {
        /*--
        require '../../onlineshopsolution/register/isValid.inc.php';
        //*/
        return (count($this->errorMessages) === 0);
    }

    /**
     * Process the user input, sent with a POST request
     *
     * Writes the data with addUser() into table onlineshop.user.
     * On success the user is redirected to index.php with View::redirectTo().
     *
     * @throws DatabaseException
     */
    protected function business(): void
    {
        $this->addUser();
        /*--
        require '../../onlineshopsolution/register/business.inc.php';
        //*/
    }


    /**
     * email of the POST-Array is checked for uniqueness against the table onlineshop.user.
     *
     * @return bool true, if email doesn't exist.
     *              false, if email exists.
     * @throws DatabaseException
     */
    private function isUniqueEmail(): bool
    {
        /*--
        require '../../onlineshopsolution/register/isUniqueEmail.inc.php';
        if (count($rows) === 0) {
            return true;
        } else {
            return false;
        }
        //*/
        //##
        return true;
        //*/
    }

    /**
     * Stores the data in the table onlineshop.user
     *
     * The field active stores a MD5-Hash to determine, that a two-phase authentication has not been finished yet.
     * If active is set to NULL, when clicking a link with this hash sent via email, the user can log in.
     * @see login.php
     * role has a default value (user) and can be left empty, if you allow only normal users to register via this form.
     * date_registered can be omitted or filled with NOW(), to store the current timestamp.
     * phone, mobile und fax are not required and can be null.
     * All other fields are directly stored to the table onlineshop.user.
     *
     * To test, if a login with login.php works with the current data,
     * set onlineshop.user.active to null with PHPMyAdmin
     *
     * @throws DatabaseException
     */
    private function addUser(): void
    {
        /*--
        require '../../onlineshopsolution/register/addUser.inc.php';
        //*/
    }
}

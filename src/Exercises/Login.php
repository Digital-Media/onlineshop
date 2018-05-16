<?php
namespace Exercises;

use Fhooe\NormForm\Core\AbstractNormForm;
use Fhooe\NormForm\Parameter\GenericParameter;
use Fhooe\NormForm\Parameter\PostParameter;
use Fhooe\NormForm\View\View;
use DBAccess\DBAccess;
use Utilities\Utilities;

/**
 * Das  Login-Formular implementiert das Einloggen in den OnlineShop.
 *
 * Das Login-Formular setzt auf der ojectorientieren Klasse TNormform und den Smarty-Templates auf.
 * Weiters benötigt es die Klasse DBAccess für Datenbankzugriffe, die die Klasse FileAccess ersetzt.
 * Durch die Verwendung von PDO Prepared Statements sind keine weiteren Maßnahmen gegen SQL-Injection notwendig.
 * XSS wird von der Klasse View verhindert für mit POST abgeschickte Eingabefelder
 * Bei erfolgreichem Login wird $_SESSION[LOGGEDIN] mit einem verschlüsselten Wert belegt.
 *
 * Die Klasse ist final, da es keinen Sinn macht, davon noch weitere Klassen abzuleiten.
 *
 * @package Onlineshop
 * @version 2018
 * @author  Martin Harrer <martin.harrer@fh-hagenberg.at>
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
     * @throws DatabaseException is thrown by all methods of $this->dbAccess and not treated here.
     *         The exception is treated in the try-catch block of the php script, that initializes this class.
     */
    public function __construct(View $defaultView)
    {
        parent::__construct($defaultView);
        /*--
        require '../../onlineshopsolution/login/construct.inc.php';
        //*/
    }

    /**
     * Validates the user input
     *
     * Pflichtfelder email, password
     * Die Kombination email + password wird gegen die Datenbank geprüft @see Login::authenitcateUser()
     * Fehlermeldungen werden im Array $errorMessages[] gesammelt.
     *
     * Abstract methods of the class AbstractNormform have to be implemented in the derived class.
     *
     * @return bool true, if $errorMessages is empty, else false
     */
    protected function isValid(): bool
    {
        /*--
        require '../../onlineshopsolution/login/isValid.inc.php';
        //*/
        //##
        $this->authenticateUser();
        //*/
        $this->currentView->setParameter(new GenericParameter("errorMessages", $this->errorMessages));
        return (count($this->errorMessages) === 0);
    }

    /**
     * Process the user input, sent with a POST request
     *
     * Die eingegebenen Daten werden nur validiert @see Login::isValid().
     * Daher erfolgt hier bei erfolgreicher Überprüfung von email+password nur noch die
     * Rückleitung auf Seiten, die durch ein Login geschützt sind, und daher User,
     * die noch nicht eingeloggt sind auf login.php weiterleiten.
     *
     * Abstract methods of the class AbstractNormform have to be implemented in the derived class.
     */
    protected function business(): void
    {
        isset($_SESSION[REDIRECT]) ? $redirect= $_SESSION['redirect'] : $redirect='index.php';
        View::redirectTo($redirect);
    }

    /**
     * Validiert email und password
     *
     * Optional wird die sesssion_id nach erfolgreichem Login neu generiert (session_regenerate_id(),
     * um Session Highjacking zu erschweren.
     * Danach müssen die session_ids für die aktuelle Session im Warenkorb (Tabelle onlineshop.cart) ebenfalls
     * erneuert werden.
     *
     * @return bool true, wenn email+password einem Datensatz in onlineshop.user entsprechen. false, wenn das
     *                    nicht der Fall ist.
     * @throws DatabaseException is thrown by all methods of $this->dbAccess and not treated here.
     *         The exception is treated in the try-catch block of the php script, that initializes this class.
     */
    private function authenticateUser()
    {
        //##
        $_SESSION['iduser']=1;
        $_SESSION[IS_LOGGED_IN] = Utilities::generateLoginHash();
        $_SESSION['first_name']='John';
        $_SESSION['last_name']='Doe';
        return true;
        //*/
        // copy solution from onlineshopsolution/login/authenticateUser.inc.php here to make solution work.
        // require doesn't work in this case
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
        //
    }

    /**
     * Erneuert die session_ids in der Tabelle onlineshop.cart, wenn bei erfolgreichem Login,
     * die session_ids neu generiert wurden.
     *
     * @return bool true, wenn das update gut gegangen ist. false, wenn das nicht der Fall ist.
     * @throws DatabaseException is thrown by all methods of $this->dbAccess and not treated here.
     *         The exception is treated in the try-catch block of the php script, that initializes this class.
     */
    private function updateCart($old_session_id, $new_session_id)
    {
        //##
        return true;
        //*/
        /*--
        require '../../onlineshopsolution/login/updateCart.inc.php';
        //*/
    }
}

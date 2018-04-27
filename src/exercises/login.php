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
     *  Konstanten für ein HTML Attribute <input name='email' id='email' ... >,
     *  <label for='email' ... > --> $_POST[EMAIL].
     */
    const EMAIL = "email";
    const PASSWORD = "password";

    /**
     * @var handler dbAccess Datenbankhandler für den Datenbankzugriff
     */
    private $dbAccess;

    /**
     * Login Constructor.
     *
     * Ruft den Constructor der Klasse TNormform auf.
     * Erzeugt den Datenbankhandler mit der Datenbankverbindung
     * Die übergebenen Konstanten finden sich in src/defines.inc.php
     */
    public function __construct(View $defaultView, $templateDir = "templates", $compileDir = "templates_c")
    {
        parent::__construct($defaultView, $templateDir, $compileDir);
        /*--
        require '../../onlineshopsolution/login/construct.inc.php';
        //*/
    }

    /**
     * Validiert den Benutzerinput
     *
     * Pflichtfelder email, password
     * Die Kombination email + password wird gegen die Datenbank geprüft @see Login::authenitcateUser()
     * Fehlermeldungen werden im Array $errorMessages[] gesammelt.
     *
     * Abstracte Methode in der Klasse TNormform und muss daher hier implementiert werden
     *
     * @return bool true, wenn $errorMessages leer ist. Ansonsten false
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
     * Verarbeitet die Benutzereingaben, die mit POST geschickt wurden
     *
     * Die eingegebenen Daten werden nur validiert @see Login::isValid().
     * Daher erfolgt hier bei erfolgreicher Überprüfung von email+password nur noch die
     * Rückleitung auf Seiten, die durch ein Login geschützt sind, und daher User,
     * die noch nicht eingeloggt sind auf login.php weiterleiten.
     *
     * @see src/defines.inc.php REDIRECT_PAGES.
     *
     * Abstracte Methode in der Klasse TNormform und muss daher hier implementiert werden
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
     * @throws DatabaseException Diese wird von allen $this->dbAccess Methoden geworfen und hier nicht behandelt.
     *         Die Exception wird daher nochmals weitergereicht (throw) und erst am Ende des Scripts behandelt.
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
     * @throws DatabaseException Diese wird von allen $this->dbAccess Methoden geworfen und hier nicht behandelt.
     *         Die Exception wird daher nochmals weitergereicht (throw) und erst am Ende des Scripts behandelt.
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

<?php
namespace Exercises;

use Fhooe\NormForm\Core\AbstractNormForm;
use Fhooe\NormForm\Parameter\GenericParameter;
use Fhooe\NormForm\Parameter\PostParameter;
use Fhooe\NormForm\View\View;
use DBAccess\DBAccess;
use Utilities\Utilities;

/*
 * Das  Registrier-Formular setzt die Registrierung im OnlineShop um.
 *
 * Das Registrier-Formular setzt auf der ojectorientieren Klasse TNormform und den Smarty-Templates auf.
 * Weiters benötigt es die Klasse DBAccess für Datenbankzugriffe, die die Klasse FileAccess ersetzt.
 * Durch die Verwendung von PDO Prepared Statements sind keine weiteren Maßnahmen gegen SQL-Injection notwendig.
 * Im Erfolgsfall werden die Benutzerdaten in der Tabelle onlineshop.users gespeichert.
 *
 * Die Klasse ist final, da es keinen Sinn macht, davon noch weitere Klassen abzuleiten.
 *
 * @author Martin Harrer <martin.harrer@fh-hagenberg.at>
 * @package dab3
 * @version 2016
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
    const PASSWORDREPEAT = "passwordrepeat";

    /**
     * @var string $dbAccess  Database handler for access to database
     */
    private $dbAccess;

    /**
     * Register Constructor.
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
        require '../../onlineshopsolution/register/construct.inc.php';
        //*/
    }

    /**
     * Validates the user input
     *
     * email wird gegen einen regulären Ausdruck geprüft, der in Utilities::isEmail() festgelegt wird
     * Browser lässt bei type="email" einiges durch, das durch isEmail gefiltert wird
     * @example m@m Email, die von cliqz-Browser zugelassen wird, von Utilities::isEmail() nicht
     * zusätzlich wird email gegen die Tabelle onlineshop.user geprüft, ob sie dort bereits vorkommt.
     * password wird mit einem regulären Ausdruck verglichen der in Utilitie::isPassword festgelegt ist
     * Von den Feldern phone, mobile und fax muss mindestens eines gefüllt sein.
     * Wenn befüllt werden sie gegen Utilities::isPhone() geprüft.
     * Die restlichen Felder sind Pflichtfelder. Das wird mit TNormform::isEmptyPostField sichergestellt
     * Fehlermeldungen werden im Array $errorMessages[] gesammelt.
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
        $this->currentView->setParameter(new GenericParameter("errorMessages", $this->errorMessages));
        return (count($this->errorMessages) === 0);
    }

    /**
     * Process the user input, sent with a POST request
     *
     * Schreibt mit addUser() die eingegebenen Daten in die Tabelle onlineshop.user
     * Wenn keine Exception auftritt wird mit View::redirectTo() auf die Seite index.php weitergeleitet
     *
     * Abstract methods of the class AbstractNormform have to be implemented in the derived class.
     *
     * @throws DatabaseException is thrown by all methods of $this->dbAccess and not treated here.
     *         The exception is treated in the try-catch block of the php script, that initializes this class.
     */
    protected function business(): void
    {
        $this->addUser();
        View::redirectTo('index.php');
    }


    /**
     * Emailadresse aus dem POST-Array wird gegen die Tabelle onlineshop.user geprüft,
     * ob sie dort bereits vorhanden ist.
     * @return bool true, wenn email nocht nicht vorhanden ist.
     *              false, wenn bereits ein Eintrag mit dieser email existiert
     * @throws DatabaseException is thrown by all methods of $this->dbAccess and not treated here.
     *         The exception is treated in the try-catch block of the php script, that initializes this class.
     */
    private function isUniqueEmail()
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
     * Schreibt die eingegebenen Daten in die Tabelle onlineshop.user
     *
     * Ins Feld active wird ein MD5-Hash geschrieben, um festzulegen,
     * dass die Zweiphasenauthentifizierung noch nicht abgeschlossen ist.
     * Wird active später auf NULL gesetzt, ist die Authentifizierung abgeschlossen und der Benutzer kann sich einloggen
     * @see login.php
     * role ist mit einem Default-Wert (user) vorbelegt und kann daher auch weggelassen werden
     * date_registered kann weggelassen werden oder mit NOW() vorbelegt werden, um den aktuellen Zeitstempel einzutragen
     * phone, mobile und fax sind keine Pflichtfelder und werden einfach mit Utilities::sanitizeFilter()
     * in die Datenbank geschrieben.
     * Alle restlichen Felder werden mit Utilities::sanitizeFilter() abgesichert in die Datenbank geschrieben.
     *
     * Zum Testen, ob man sich mit login.php einloggen kann, im PHPMyAdmin in onlineshop.user
     * beim neuen Datensatz bei Feld onlineshop.user.active das Häkchen für NULL setzen.
     *
     * @return bool true, wenn das Schreiben in die Tabelle onlineshop.user erfolgreich ist.
     * @throws DatabaseException is thrown by all methods of $this->dbAccess and not treated here.
     *         The exception is treated in the try-catch block of the php script, that initializes this class.
     */
    private function addUser()
    {
        /*--
        require '../../onlineshopsolution/register/addUser.inc.php';
        //*/
        //##
        return true;
        //*/
    }
}

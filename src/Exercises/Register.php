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
     *  Konstanten für ein HTML Attribute <input name='firstname' id='firstname' ... >,
     * <label for='firstname' ... > --> $_POST[FIRSTNAME].
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
    /*
     * Konstante für die Umlenkung auf die Login-Seite.
     * Nach erfolgreicher Registrierung wird man zur Loginseite weitergeleitet
     */
    const LOGIN = "login.php";

    /**
     * @var string $dbAccess Datenbankhandler für den Datenbankzugriff
     */
    private $dbAccess;

    /**
     * Register Constructor.
     *
     * Ruft den Constructor der Klasse TNormform auf.
     * Erzeugt den Datenbankhandler mit der Datenbankverbindung
     * Die übergebenen Konstanten finden sich in src/defines.inc.php

     */
    public function __construct(View $defaultView, $templateDir = "templates", $compileDir = "templates_c")
    {
        parent::__construct($defaultView, $templateDir, $compileDir);
        /*--
        require '../../onlineshopsolution/register/construct.inc.php';
        //*/
    }

    /**
     * Validiert den Benutzerinput
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
     * Abstracte Methode in der Klasse TNormform und muss daher hier implementiert werden
     *
     * @return bool true, wenn $errorMessages leer ist. Ansonsten false
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
     * Verarbeitet die Benutzereingaben, die mit POST geschickt wurden
     *
     * Schreibt mit addUser() die eingegebenen Daten in die Tabelle onlineshop.user
     * Wenn keine Exception auftritt wird mit Utilities::redirectTo(LOGIN) auf die Seite login.php weitergeleitet
     *
     * Abstracte Methode in der Klasse TNormform und muss daher hier implementiert werden
     *
     * @throws DatabaseException Diese wird von allen $this->dbAccess Methoden geworfen und hier nicht behandelt.
     *         Die Exception wird daher nochmals weitergereicht (throw) und erst am Ende des Scripts behandelt.
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
     * @throws DatabaseException wird von allen $this->dbAccess Methoden geworfen und hier nicht behandelt.
     *         Die Exception wird daher nochmals weitergereicht (throw) und erst am Ende des Scripts behandelt.
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
     * @throws DatabaseException Diese wird von allen $this->dbAccess Methoden geworfen und hier nicht behandelt.
     *         Die Exception wird daher nochmals weitergereicht (throw) und erst am Ende des Scripts behandelt.
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

<?php
namespace Exercises;

use Elasticsearch\ClientBuilder;
use Fhooe\NormForm\Core\AbstractNormForm;
use Fhooe\NormForm\Parameter\GenericParameter;
use Fhooe\NormForm\Parameter\PostParameter;
use Fhooe\NormForm\View\View;
use DBAccess\DBAccess;
use Twig\NodeVisitor\SafeAnalysisNodeVisitor;
use Utilities\Utilities;
use PDO;

/**
 * Class DBDEmo implements a demo page for the class AbstractNormForm combined with the class DBAccess of OnlineShop
 *
 * This class is initialized by htdocs/dbdemo.php, is derived from the class AbstractNormForm and uses TWIG templates.
 * Additionally the class DBAccess is used for database access.
 * DBAccess replaces the class FileAccess of the project PHPintro.
 * Due to the usage of PDO Prepared Statements no further steps are necessary to avoid SQL Injection in this use case.
 * XSS is prevented by the TWIG template engine, that escapes variables sent to a template automatically.
 *
 * This page lists the content of onlineshop.product_category and adds additional categories.
 *
 * Class DBAccess is final, because it makes no sense to derive a class from it.
 *
 * @author  Martin Harrer <martin.harrer@fh-hagenberg.at>
 * @package onlineshop
 * @version 2018
 */
final class Search extends AbstractNormForm
{
    // make trait Utilities accessible via $this->
    use Utilities;
    /**
     * Constant for a HTML attribute in <input name='ptype' id='ptype' ... >, <label for='ptype' ... >
     * --> $_POST[self::PTYPE]
     */
    const SEARCH = 'search';

    /**
     * @var object $dbAccess  Database handler for access to database
     */
    private $dbAccess;

    /**
     * @var object $esClient  Client to access ElasticSearch
     */
    private $esClient;

    /**
     * @var string $start offset value for pagination
     */
    private $start;

    /**
     * DBDemo Constructor.
     *
     * Calls constructor of class AbstractNormForm.
     * Creates a database handler for the database connection.
     * Creates the Handler for ElasticSearch access
     * The assigned constants can be found in src/defines.inc.php
     *
     * @param View $defaultView Holds the initial @View object used for displaying the form.
     *
     * @throws \DBAccess\DatabaseException
     */
    public function __construct(View $defaultView)
    {
        parent::__construct($defaultView);
        //%%search/construct
    }

    /**
     * Validates the user input
     *
     * The product category ptype is tested if it is empty.
     * Additionally it is validated with a regex given by Utilities::isSingleWord().
     * Due to "use Utilities" at the begin of this class, $this->isSingleWord() is also possible.
     * The trait is part of the current class then.
     * Error messages are written to the array $errorMessages[].
     *
     * Abstract methods of the class AbstractNormform have to be implemented in the derived class.
     *
     * @return bool true, if $errorMessages is empty, else false
     */
    protected function isValid(): bool
    {
        if ($this->isEmptyPostField(self::SEARCH) && !isset($_POST['next']) && !isset($_POST['previous'])) {
            $this->errorMessages[self::SEARCH] = "Please enter a search term.";
        }
        if (!$this->isEmptyPostField("start") && !Utilities::isInt($_POST['start'])) {
            $this->errorMessages['start'] = "Please enter a valid start value.";
        }
        $this->currentView->setParameter(new GenericParameter("errorMessages", $this->errorMessages));
        return (count($this->errorMessages) === 0);
    }

    /**
     * Process the user input, sent with a POST request
     *
     * Stores the search term in hidden input field
     * Sets the start parameter for pagination
     * Abstract methods of the class AbstractNormform have to be implemented in the derived class.
     *
     * @throws \DBAccess\DatabaseException is thrown by all methods of $this->dbAccess and not treated here.
     *         The exception is treated in the try-catch block of the php script, that initializes this class.
     */
    protected function business(): void
    {
        if (isset($_POST[self::SEARCH])) {
            $search = $_POST[self::SEARCH];
            $this->currentView->setParameter(new GenericParameter("storeSearch", $_POST[self::SEARCH]));
        }
        if ($this->isEmptyPostField('start')) {
            $this->start = 0;
            $this->currentView->setParameter(new GenericParameter("startValue", $this->start));
        }

        if (isset($_POST['next'])) {
            $search = $_POST['next'];
            $this->currentView->setParameter(new GenericParameter("storeSearch", $_POST['next']));
            $this->start = intval($_POST['start']) + 2;
            $this->currentView->setParameter(new GenericParameter("startValue", $this->start));
        }
        if (isset($_POST['previous'])) {
            $search = $_POST['previous'];
            $this->currentView->setParameter(new GenericParameter("storeSearch", $_POST['previous']));
            $this->start = intval($_POST['start']) - 2;
            $this->currentView->setParameter(new GenericParameter("startValue", $this->start));
        }

        $result = $this->doElasticSearch($search);
        if (isset($result['hits'])) {
            $this->currentView->setParameter(new GenericParameter("totalValue", $result['hits']['total']));
            if (count($result['hits']['hits']) !== 0) {
                $this->currentView->setParameter(new GenericParameter("pageArray", $this->fillPageArray($result)));
            }
        }
    }

    /**
     * Searches for entries in Elasticsearch, that match the given serach terms
     *
     * @param  string $search search terms given in search field
     * @return array $result Result of the ElasticSearch request
     */
    private function doElasticSearch(string $search): array
    {
        //##%%
        return [];
        //#%#%
        //%%search/doElasticSearch
    }

    /**
     * Returns an array to display all entries found by ElasticSearch of onlineshop.product on the current page.
     *
     * @param  array $result Result of search in ElasticSearch
     * @return array $result Result set of database query.
     *
     * @throws \DBAccess\DatabaseException
     */
    private function fillPageArray(array $result): array
    {
        //##%%
        return [];
        //#%#%*/
        //%%search/fillPageArray
    }
}

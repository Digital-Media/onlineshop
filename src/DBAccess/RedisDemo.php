<?php
namespace DBAccess;

use Fhooe\NormForm\Core\AbstractNormForm;
use Fhooe\NormForm\Parameter\GenericParameter;
use Fhooe\NormForm\Parameter\PostParameter;
use Fhooe\NormForm\View\View;
use Utilities\Utilities;
use Utilities\LogWriter;
use Redis;

/**
 * Class RedisDemo implements a demo page for the class AbstractNormForm to show how to use Redis for sessions, logging and caching.
 *
 * This class is initialized by htdocs/redisdemo.php, is derived from the class AbstractNormForm and uses TWIG templates.
 * This page lists the content of a Redis database and gives a input field to add an additional key.
 *
 * @author  Martin Harrer <martin.harrer@fh-hagenberg.at>
 * @package onlineshop
 * @version 2018
 */
final class RedisDemo extends AbstractNormForm
{
    /**
     * Constant for a HTML attribute in <input name='ptype' id='ptype' ... >, <label for='ptype' ... >
     * --> $_POST[self::PTYPE]
     */
    const REDIS_KEY = 'redis_key';

    /**
     * @var string $redis  Database handler for access to redis database
     */
    private $redis;
    
    /**
     * RedisDemo Constructor.
     *
     * Sets the current view.
     * Initializes a logwriter instance with a RedisHandler, debug level and cap size.
     * Initializes a handler for Redis. Opens a connections and sends the password.
     *
     * @param View $defaultView Holds the initial @View object used for displaying the form.
     */
    public function __construct(View $defaultView)
    {
        parent::__construct($defaultView);
        $this->logWriter = LogWriter::getInstance('redis', 100, 5);
        //$this->logWriter->logInfo("Logging with Redis");
        //$x = 1/0;
        //$this->hugo;
        $this->redis = new Redis();
        $this->redis->pconnect('localhost', 6379);
        $this->redis->auth('geheim');
        $this->redis->sAdd('myset', 'stuff');
        $this->redis->sAdd('myset', 'new');
        $this->redis->sAdd('myset', 'cool');
        $ttl = $this->redis->ttl(self::REDIS_KEY);
        $count = isset($_SESSION['count']) ? $_SESSION['count'] : 1;
        $_SESSION['count'] = ++$count;
        --$count;
        $this->errorMessages[] = "PHP Session Handler set to: " . ini_get("session.save_handler");
        $this->errorMessages[] = "PHP Session Save Path set to: " . ini_get("session.save_path");
        $this->errorMessages[] = "Monolog logs to Redis!";
        $this->statusMessage = "Session Counter is $count times and TTL is $ttl";
        $this->currentView->setParameter(new GenericParameter("errorMessages", $this->errorMessages));
        $this->currentView->setParameter(new GenericParameter("statusMessage", $this->statusMessage));
        $this->currentView->setParameter(new GenericParameter("pageArray", $this->ShowRedisEntries()));
    }

    /**
     * Validates the user input
     *
     * @return bool true, if $errorMessages is empty, else false
     */
    protected function isValid(): bool
    {
        if ($this->isEmptyPostField(self::REDIS_KEY)) {
            $this->errorMessages[self::REDIS_KEY] = "Please enter a Redis Key.";
        }
        $this->currentView->setParameter(new GenericParameter("errorMessages", $this->errorMessages));
        return (count($this->errorMessages) === 3);
    }

    /**
     * Process the user input, sent with a POST request
     * Store input in Redis
     * Read TimeToLive for this key
     */
    protected function business(): void
    {
        $this->redis->setex(self::REDIS_KEY, 10, $_POST[self::REDIS_KEY]);
        $ttl = $this->redis->ttl(self::REDIS_KEY);
        $this->statusMessage = "TTL for " . self::REDIS_KEY . " is $ttl.";
        $this->currentView->setParameter(new GenericParameter("statusMessage", $this->statusMessage));
        $this->currentView->setParameter(new GenericParameter("pageArray", $this->ShowRedisEntries()));
        $this->currentView->setParameter(new PostParameter(self::REDIS_KEY, true));
    }
    
    /**
     * Read all Keys from Redis
     * They are handled depending on their type
     * string
     * set
     * list
     * 
     * @return array $redis_entries Content of all keys in the current Redis database
     */
    
    protected function ShowRedisEntries() 
    {
        $redis_entries=[];
        $keys = $this->redis->keys('*');
        foreach ($keys as $key => $value) {
            // TYPE string
            if ($this->redis->type($value) === 1) {
                $redis_entries[$value] = $this->redis->get($value);
            }
            // TYPE set
            if ($this->redis->type($value) === 2) {
                $set = $this->redis->sMembers($value);
                foreach ($set as $entry => $item) {
                    $redis_entries[$value . $entry]=$item;
                }
            }
            // TYPE list
            if ($this->redis->type($value) === 3) {
                $list = $this->redis->lRange($value, 0, -1);
                foreach ($list as $entry => $item) {
                    $redis_entries[$value . $entry]=$item;
                }
            }            
        }
        if ($this->redis->exists(self::REDIS_KEY)) {
            $redis_entries[self::REDIS_KEY] = $this->redis->get(self::REDIS_KEY);
        }
        return $redis_entries;
    }
}

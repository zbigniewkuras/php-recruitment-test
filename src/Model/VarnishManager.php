<?php

namespace Snowdog\DevTest\Model;

use Snowdog\DevTest\Core\Database;

class VarnishManager
{

    /**
     * @var Database|\PDO
     */
    private $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function getAllByUser(User $user)
    {
        $userId = $user->getUserId();
        /** @var \PDOStatement $query */
        $query = $this->database->prepare('SELECT * FROM varnishes WHERE user_id = :userId');
        $query->bindParam(':userId', $userId, \PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll(\PDO::FETCH_CLASS, Varnish::class);
    }

    public function getWebsites(Varnish $varnish)
    {
        $varnishId = $varnish->getVarnishId();
        /** @var \PDOStatement $query */
        $query = $this->database->prepare('SELECT w.* FROM websites as w 
             LEFT JOIN varnishes_websites as vw ON vw.website_id = w.website_id WHERE vw.varnish_id = :varnishId');
        $query->bindParam(':varnishId', $varnishId, \PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll(\PDO::FETCH_CLASS, Website::class);
    }

    public function getByWebsite(Website $website)
    {
        $websiteId = $website->getWebsiteId();
        /** @var \PDOStatement $query */
        $query = $this->database->prepare('SELECT v.* FROM varnishes as v 
             LEFT JOIN varnishes_websites as vw ON vw.varnish_id = v.varnish_id WHERE vw.website_id = :websiteId');
        $query->bindParam(':websiteId', $websiteId, \PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll(\PDO::FETCH_CLASS, Varnish::class);
    }

    public function create(User $user, $ip)
    {
        $userId = $user->getUserId();
        /** @var \PDOStatement $statement */
        $statement = $this->database->prepare('INSERT INTO varnishes (user_id, ip) VALUES (:userId, :ip)');
        $statement->bindParam(':userId', $userId, \PDO::PARAM_INT);
        $statement->bindParam(':ip', $ip, \PDO::PARAM_STR);
        $statement->execute();
        return $this->database->lastInsertId();
    }

    public function link($varnishId, $websiteId)
    {
        /** @var \PDOStatement $statement */
        $statement = $this->database->prepare('INSERT INTO varnishes_websites (varnish_id, website_id) VALUES (:varnishId, :websiteId)');
        $statement->bindParam(':varnishId', $varnishId, \PDO::PARAM_INT);
        $statement->bindParam(':websiteId', $websiteId  , \PDO::PARAM_INT);
        $statement->execute();
        return $this->database->lastInsertId();
    }

    public function unlink($varnishId, $websiteId)
    {
        /** @var \PDOStatement $statement */
        $statement = $this->database->prepare('DELETE FROM varnishes_websites WHERE varnish_id = :varnishId AND website_id = :websiteId');
        $statement->bindParam(':varnishId', $varnishId, \PDO::PARAM_INT);
        $statement->bindParam(':websiteId', $websiteId  , \PDO::PARAM_INT);
        $statement->execute();
        return $this->database->lastInsertId();
    }

}
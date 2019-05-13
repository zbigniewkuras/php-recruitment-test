<?php
namespace Snowdog\DevTest\Model;

use Snowdog\DevTest\Core\Database;

class PageManager
{

    /**
     *
     * @var Database|\PDO
     */
    private $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function getAllByWebsite(Website $website)
    {
        $websiteId = $website->getWebsiteId();
        /** @var \PDOStatement $query */
        $query = $this->database->prepare('SELECT * FROM pages WHERE website_id = :website');
        $query->bindParam(':website', $websiteId, \PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll(\PDO::FETCH_CLASS, Page::class);
    }

    public function create(Website $website, $url)
    {
        $websiteId = $website->getWebsiteId();
        /** @var \PDOStatement $statement */
        $statement = $this->database->prepare('INSERT INTO pages (url, website_id) VALUES (:url, :website)');
        $statement->bindParam(':url', $url, \PDO::PARAM_STR);
        $statement->bindParam(':website', $websiteId, \PDO::PARAM_INT);
        $statement->execute();
        return $this->database->lastInsertId();
    }

    /**
     *
     * @param Page $page
     * @param int $warmedAt
     * @return number
     */
    public function updateWarmedAt(Page $page, $warmedAt)
    {
        $pageId = $page->getPageId();
        /** @var \PDOStatement $statement */
        $statement = $this->database->prepare('UPDATE pages SET warmed_at = :warmedAt where page_id = :pageId');
        $statement->bindParam(':pageId', $pageId, \PDO::PARAM_INT);
        $statement->bindParam(':warmedAt', $warmedAt, \PDO::PARAM_STR);
        $statement->execute();

        return $statement->rowCount();
    }

    /**
     * 
     * @param User $user
     * @return number
     */
    public function getCountByUser(User $user)
    {
        $userId = $user->getUserId();
        /** @var \PDOStatement $query */
        $query = $this->database->prepare('SELECT COUNT(*) as num_pages FROM pages as p LEFT JOIN websites as w ON w.website_id = p.website_id  WHERE w.user_id = :user');
        $query->bindParam(':user', $userId, \PDO::PARAM_INT);
        $query->execute();
        return (int)$query->fetchColumn();
    }
    
    /**
     * 
     * @param User $user
     * @param string $orderDir
     * @return mixed
     */
    public function getByUser(User $user, $orderDir = 'ASC')
    {
        $userId = $user->getUserId();
        /** @var \PDOStatement $query */
        $query = $this->database->prepare('SELECT p.* FROM pages as p LEFT JOIN websites as w ON w.website_id = p.website_id  WHERE w.user_id = :user ORDER BY p.warmed_at '.$orderDir.' LIMIT 1');
        $query->bindParam(':user', $userId, \PDO::PARAM_INT);
        $query->execute();
        return $query->fetchObject(Page::class);
    }
}
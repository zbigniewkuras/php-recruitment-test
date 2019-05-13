<?php

namespace Snowdog\DevTest\Controller;

use Snowdog\DevTest\Model\User;
use Snowdog\DevTest\Model\UserManager;
use Snowdog\DevTest\Model\WebsiteManager;
use Snowdog\DevTest\Model\PageManager;

class IndexAction
{

    /**
     * @var WebsiteManager
     */
    private $websiteManager;

    /**
     * @var User
     */
    private $user;

    /**
     * @var PageManager
     */
    private $pageManager;
    
    public function __construct(UserManager $userManager, WebsiteManager $websiteManager, PageManager $pageManager)
    {
        $this->websiteManager = $websiteManager;
        $this->pageManager = $pageManager;
        if (isset($_SESSION['login'])) {
            $this->user = $userManager->getByLogin($_SESSION['login']);
        }
    }

    protected function getWebsites()
    {
        if($this->user) {
            return $this->websiteManager->getAllByUser($this->user);
        } 
        return [];
    }

    public function execute()
    {
        require __DIR__ . '/../view/index.phtml';
    }
    
    /**
     * 
     * @return number|array
     */
    protected function getCountPages()
    {
        if($this->user) {
            return $this->pageManager->getCountByUser($this->user);
        }
        return [];
    }
    
    /**
     * 
     * @return mixed|array
     */
    protected function getLeastRecentlyVisitedPage()
    {
        if($this->user) {
            return $this->pageManager->getByUser($this->user);
        }
        return [];
    }
    
    /**
     * 
     * @return mixed|array
     */
    protected function getMostRecentlyVisitedPage()
    {
        if($this->user) {
            return $this->pageManager->getByUser($this->user, 'DESC');
        }
        return [];
    }
}
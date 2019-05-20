<?php

namespace Snowdog\DevTest\Controller;

use Snowdog\DevTest\Model\UserManager;
use Snowdog\DevTest\Model\VarnishManager;

class CreateVarnishUnlinkAction
{
    /**
     * @var UserManager
     */
    private $userManager;
    /**
     * @var VarnishManager
     */
    private $varnishManager;

    public function __construct(UserManager $userManager, VarnishManager $varnishManager)
    {
        $this->userManager = $userManager;
        $this->varnishManager = $varnishManager;
    }

    public function execute()
    {
        $varnishId = (int)$_POST['varnish_id'];
        $websiteId = (int)$_POST['website_id'];
        
        try {
            $link = $this->varnishManager->unlink($varnishId, $websiteId);
            $message = 'Unlinked!';
        } catch (\Exception $e) {
            $message = $e->getMessage();
        }
        $data = ['message'=> $message, 'status'];
        header('Content-Type: application/json');
        echo json_encode($data);
        exit(0);
    }
}
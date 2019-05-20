<?php
namespace Snowdog\DevTest\Controller;

use Snowdog\DevTest\Model\UserManager;
use Snowdog\DevTest\Model\VarnishManager;
use Snowdog\DevTest\Model\User;

class CreateVarnishAction
{

    /**
     *
     * @var UserManager
     */
    private $userManager;

    /**
     *
     * @var User $user
     */
    private $user;

    /**
     *
     * @var VarnishManager
     */
    private $varnishManager;

    public function __construct(UserManager $userManager, VarnishManager $varnishManager)
    {
        $this->userManager = $userManager;
        if (isset($_SESSION['login'])) {
            $this->user = $this->userManager->getByLogin($_SESSION['login']);
        }
        $this->varnishManager = $varnishManager;
    }

    public function execute()
    {
        if (!$this->user) {
            $_SESSION['flash'] = 'Forbidden access!';
            header('Location: /varnish');
            return;
        }
        $ip = $_POST['ip'];
    
        $valid = filter_var($ip, FILTER_VALIDATE_IP);
        if (! $valid) {
            $_SESSION['flash'] = 'IP value is incorrect!';
        } else {
            $this->varnishManager->create($this->user, $ip);
        }

        header('Location: /varnish');
    }
}
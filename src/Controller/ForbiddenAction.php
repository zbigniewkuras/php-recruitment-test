<?php
namespace Snowdog\DevTest\Controller;

class ForbiddenAction
{

    public function execute()
    {
        include __DIR__ . '/../view/403.phtml';
    }
}
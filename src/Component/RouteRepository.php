<?php
namespace Snowdog\DevTest\Component;

use FastRoute\RouteCollector;
use Snowdog\DevTest\Controller\ForbiddenAction;

class RouteRepository
{

    private static $instance = null;

    private $routes = [];

    const HTTP_METHOD = 'http_method';

    const ROUTE = 'route';

    const CLASS_NAME = 'class_name';

    const METHOD_NAME = 'method_name';

    const ACCESS_GUEST = 'guest';

    const ACCESS_LOGGED = 'logged';

    /**
     *
     * @return RouteRepository
     */
    public static function getInstance()
    {
        if (! self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function registerRoute($httpMethod, $route, $className, $methodName, $accessLevel = '')
    {
        $instance = self::getInstance();
        switch ($accessLevel) {
            case self::ACCESS_GUEST:
                if (self::isLoggednIn()) {
                    $className = ForbiddenAction::class;
                    $methodName = 'execute';
                }
                break;
            case self::ACCESS_LOGGED:
                if (! self::isLoggednIn()) {
                    $className = ForbiddenAction::class;
                    $methodName = 'execute';
                }
                break;
        }
        $instance->addRoute($httpMethod, $route, $className, $methodName);
    }

    public function __invoke(RouteCollector $r)
    {
        foreach ($this->routes as $route) {
            $r->addRoute($route[self::HTTP_METHOD], $route[self::ROUTE], [
                $route[self::CLASS_NAME],
                $route[self::METHOD_NAME]
            ]);
        }
    }

    private function addRoute($httpMethod, $route, $className, $methodName)
    {
        $this->routes[] = [
            self::HTTP_METHOD => $httpMethod,
            self::ROUTE => $route,
            self::CLASS_NAME => $className,
            self::METHOD_NAME => $methodName
        ];
    }

    /**
     * 
     * @return boolean
     */
    private static function isLoggednIn()
    {
        return isset($_SESSION['login']);
    }
}
<?php

declare(strict_types=1);

namespace Random\Developer\Jedi;

use Barnacle\Container;
use Barnacle\Exception\NotFoundException;
use Barnacle\RegistrationInterface;
use Bone\Http\Middleware\HalCollection;
use Bone\Http\Middleware\HalEntity;
use Bone\Mvc\Router\RouterConfigInterface;
use Bone\Mvc\View\PlatesEngine;
use Doctrine\ORM\EntityManager;
use League\Route\RouteGroup;
use League\Route\Router;
use League\Route\Strategy\JsonStrategy;
use Random\Developer\Jedi\Controller\JediApiController;
use Random\Developer\Jedi\Controller\JediController;
use Random\Developer\Jedi\Service\JediService;
use Zend\Diactoros\ResponseFactory;

class JediPackage implements RegistrationInterface, RouterConfigInterface
{
    /**
     * @param Container $c
     */
    public function addToContainer(Container $c)
    {
        /** @var PlatesEngine $viewEngine */
        $viewEngine = $c->get(PlatesEngine::class);
        $viewEngine->addFolder('jedi', 'src/Jedi/View/Jedi/');

        $c[JediService::class] = $c->factory(function (Container $c) {
            $em =  $c->get(EntityManager::class);

            return new JediService($em);
        });

        $c[JediController::class] = $c->factory(function (Container $c) {
            $service = $c->get(JediService::class);
            /** @var PlatesEngine $viewEngine */
            $viewEngine = $c->get(PlatesEngine::class);

            return new JediController($viewEngine, $service);
        });

        $c[JediApiController::class] = $c->factory(function (Container $c) {
            $service = $c->get(JediService::class);

            return new JediApiController($service);
        });
    }

    /**
     * @return string
     */
    public function getEntityPath(): string
    {
        return '/src/Jedi/Entity';
    }

    /**
     * @return bool
     */
    public function hasEntityPath(): bool
    {
        return true;
    }

    /**
     * @param Container $c
     * @param Router $router
     * @return Router
     */
    public function addRoutes(Container $c, Router $router): Router
    {
        $router->map('GET', '/jedi', [JediController::class, 'indexAction']);
        $router->map('GET', '/jedi/{id:number}', [JediController::class, 'viewAction']);
        $router->map('GET', '/jedi/create', [JediController::class, 'createAction']);
        $router->map('GET', '/jedi/edit/{id:number}', [JediController::class, 'editAction']);
        $router->map('GET', '/jedi/delete/{id:number}', [JediController::class, 'deleteAction']);

        $router->map('POST', '/jedi/create', [JediController::class, 'createAction']);
        $router->map('POST', '/jedi/edit/{id:number}', [JediController::class, 'editAction']);
        $router->map('POST', '/jedi/delete/{id:number}', [JediController::class, 'deleteAction']);

        $factory = new ResponseFactory();
        $strategy = new JsonStrategy($factory);
        $strategy->setContainer($c);

        $router->group('/api', function (RouteGroup $route) {
            $route->map('GET', '/jedi', [JediApiController::class, 'indexAction'])->prependMiddleware(new HalCollection(5));
            $route->map('GET', '/jedi/{id:number}', [JediApiController::class, 'viewAction'])->prependMiddleware(new HalEntity());
            $route->map('POST', '/jedi', [JediApiController::class, 'createAction']);
            $route->map('PUT', '/jedi/{id:number}', [JediApiController::class, 'updateAction']);
            $route->map('DELETE', '/jedi/{id:number}', [JediApiController::class, 'deleteAction']);
        })
        ->setStrategy($strategy);

        return $router;
    }
}

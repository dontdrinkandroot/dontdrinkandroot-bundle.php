<?php


namespace Dontdrinkandroot\UtilsBundle\Routing;

use Dontdrinkandroot\UtilsBundle\Controller\EntityControllerInterface;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class EntityLoader extends Loader
{

    private $loaded = false;

    /**
     * @var KernelInterface
     */
    private $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        if (true === $this->loaded) {
            throw new \RuntimeException(sprintf('Do not add the "%s" loader twice', $this->getType()));
        }

        $parts = explode(':', $resource);
        if (2 !== count($parts)) {
            throw new \Exception('Can not process resource string');
        }

        $bundle = $parts[0];
        $controllerName = $parts[1];

        try {
            $allBundles = $this->kernel->getBundle($bundle, false);
        } catch (\InvalidArgumentException $e) {
            throw new \Exception(sprintf('Bundle "%s" not found', $bundle));
        }

        $candidates = [];
        foreach ($allBundles as $b) {
            $candidate = $b->getNamespace() . '\\Controller\\' . $controllerName . 'Controller';
            if (class_exists($candidate)) {
                $candidates[] = $candidate;
            }

            $matchingBundles[] = $b->getName();
        }

        if (0 === count($candidates)) {
            throw new \Exception('Controller not found');
        }

        if (count($candidates) > 1) {
            throw new \Exception('More than one matching candidate found');
        }

        $controllerClass = $candidates[0];

        $reflectionClass = new \ReflectionClass($controllerClass);
        if (!$reflectionClass->implementsInterface($this->getControllerClass())) {
            throw new \Exception('Controller must implement ' . $this->getControllerClass());
        }

        /** @var EntityControllerInterface $controller */
        $controller = new $controllerClass;

        $routes = $this->createRouteCollection($controller, $resource);

        $this->loaded = true;

        return $routes;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return $this->getType() === $type;
    }

    /**
     * @return string
     */
    protected function getType()
    {
        return 'ddr_entity';
    }

    /**
     * @return string
     */
    protected function getControllerClass()
    {
        return EntityControllerInterface::class;
    }

    /**
     * @param EntityControllerInterface $controller
     * @param                           $resource
     *
     * @return RouteCollection
     */
    protected function createRouteCollection(EntityControllerInterface $controller, $resource)
    {
        $routePrefix = $controller->getRoutePrefix();
        $pathPrefix = $controller->getPathPrefix();

        $routes = new RouteCollection();

        $routes->add(
            $routePrefix . '.edit',
            new Route($pathPrefix . '{id}/edit', ['_controller' => $resource . ':edit'])
        );
        $routes->add(
            $routePrefix . '.delete',
            new Route($pathPrefix . '{id}/delete', ['_controller' => $resource . ':delete'])
        );
        $routes->add(
            $routePrefix . '.detail',
            new Route($pathPrefix . '{id}', ['_controller' => $resource . ':detail'])
        );
        $routes->add($routePrefix . '.list', new Route($pathPrefix, ['_controller' => $resource . ':list']));

        return $routes;
    }
}

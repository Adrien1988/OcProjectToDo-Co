<?php

namespace App;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Routing\RouteCollectionBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        $contents = require dirname(__DIR__) . '/config/bundles.php';

        foreach ($contents as $class => $envs) {
            if ($envs['all'] ?? false || ($envs[$this->getEnvironment()] ?? false)) {
                yield new $class();
            }
        }
    }

    protected function configureContainer(\Symfony\Component\DependencyInjection\ContainerBuilder $container, LoaderInterface $loader): void
    {
        $confDir = dirname(__DIR__) . '/config';

        $loader->load($confDir . '/packages/*.{php,xml,yaml,yml}', 'glob');
        $loader->load($confDir . '/packages/' . $this->environment . '/*.{php,xml,yaml,yml}', 'glob');
        $loader->load($confDir . '/services.yaml');
    }

    protected function configureRoutes(RouteCollectionBuilder $routes): void
    {
        $confDir = dirname(__DIR__) . '/config';

        $routes->import($confDir . '/routes/*.yaml', '/', 'glob');

        if (is_dir($confDir . '/routes/' . $this->environment)) {
            $routes->import($confDir . '/routes/' . $this->environment . '/*.yaml', '/', 'glob');
        }

        if (file_exists($confDir . '/routes.yaml')) {
            $routes->import($confDir . '/routes.yaml');
        }
    }
}

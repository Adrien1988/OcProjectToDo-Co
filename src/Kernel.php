<?php

namespace App;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

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

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $confDir = dirname(__DIR__) . '/config';

        $loader->load($confDir . '/packages/*.{php,xml,yaml,yml}', 'glob');
        $loader->load($confDir . '/packages/' . $this->environment . '/*.{php,xml,yaml,yml}', 'glob');
        $loader->load($confDir . '/services.yaml');
        if (is_file($confDir . '/services_' . $this->environment . '.yaml')) {
            $loader->load($confDir . '/services_' . $this->environment . '.yaml');
        }
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $confDir = dirname(__DIR__) . '/config';

        if (is_file($confDir . '/routes.yaml')) {
            $routes->import($confDir . '/routes.yaml');
        }

        if (is_dir($confDir . '/routes/')) {
            $routes->import($confDir . '/routes/*.{php,xml,yaml,yml}', '/', 'glob');
        }

        if (is_dir($confDir . '/routes/' . $this->environment)) {
            $routes->import($confDir . '/routes/' . $this->environment . '/*.{php,xml,yaml,yml}', '/', 'glob');
        }
    }
}

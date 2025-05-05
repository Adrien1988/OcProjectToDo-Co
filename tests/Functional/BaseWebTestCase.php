<?php
namespace Tests\Functional;

use App\DataFixtures\TestFixtures;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

abstract class BaseWebTestCase extends WebTestCase
{
    protected static bool $schemaInitialized = false;

    /** @var KernelBrowser */
    protected KernelBrowser $client;

    /** @var EntityManagerInterface */
    protected EntityManagerInterface $em;

    /** @var UserPasswordHasherInterface */
    protected UserPasswordHasherInterface $hasher;

    /**
     * Override PHPUnit-Symfony’s kernel loader to force App\Kernel.
     */
    protected static function createKernel(array $options = []): KernelInterface
    {
        $class = \App\Kernel::class;
        $env   = $options['environment'] ?? 'test';
        $debug = $options['debug']       ?? false;

        return new $class($env, $debug);
    }

    protected function setUp(): void
    {
        parent::setUp();

        // reset any previous kernel
        self::ensureKernelShutdown();

        // create a fresh client (boots kernel via createKernel())
        $this->client = static::createClient();

        // grab services
        $container    = $this->client->getContainer();
        $this->em     = $container->get('doctrine')->getManager();
        $this->hasher = $container->get(UserPasswordHasherInterface::class);

        // once: drop/create schema + load fixtures
        if (!self::$schemaInitialized) {
            $this->rebuildSchema();
            $this->loadFixtures();
            self::$schemaInitialized = true;
        }
    }

    private function rebuildSchema(): void
    {
        $metadatas  = $this->em->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($this->em);
        $schemaTool->dropSchema($metadatas);
        $schemaTool->createSchema($metadatas);
    }

    private function loadFixtures(): void
    {
        // only TestFixtures (group “test”)
        $fixture = new TestFixtures($this->hasher);
        $fixture->load($this->em);
    }
}

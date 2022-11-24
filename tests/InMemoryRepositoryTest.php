<?php

declare(strict_types=1);

/*
 * This file is part of the Drewlabs package.
 *
 * (c) Sidoine Azandrew <azandrewdevelopper@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Drewlabs\Libman\Contracts\AuthBasedLibraryConfigInterface;
use Drewlabs\Libman\Contracts\AuthCredentialsInterface;
use Drewlabs\Libman\Contracts\InstallableLibraryConfigInterface;
use Drewlabs\Libman\Contracts\LibraryConfigInterface;
use Drewlabs\Libman\Contracts\LibraryConfigurationsRepositoryInterface;
use Drewlabs\Libman\Contracts\LibraryRepositoryConfigInterface;
use Drewlabs\Libman\Contracts\WebServiceLibraryConfigInterface;
use Drewlabs\Libman\InMemoryConfigurationRepository;
use Drewlabs\Libman\LibraryConfig;
use Drewlabs\Libman\Tests\Stubs\TestLibraryFactory;
use Drewlabs\Libman\YAMLDefinitionsProvider;
use PHPUnit\Framework\TestCase;

class InMemoryRepositoryTest extends TestCase
{
    public function test_create_in_memory_repository_instance()
    {
        $instance = $this->createYMLBasedRepository();
        $this->assertInstanceOf(LibraryConfigurationsRepositoryInterface::class, $instance);
    }

    public function test_repository_select_library_returns_instance_library_interface()
    {
        $instance = $this->createYMLBasedRepository();
        $library = $instance->select('drewlabs/contracts');
        $this->assertInstanceOf(InstallableLibraryConfigInterface::class, $library);
        $this->assertSame('drewlabs/contracts', $library->getPackage());
        $this->assertSame('composer', $library->getType());
        $this->assertTrue($library->isPrivate());
        $this->assertTrue(is_array($library->getRepository()));
        $this->assertInstanceOf(LibraryRepositoryConfigInterface::class, $library->getRepository()[0]);
        $this->assertSame('git@github.com:liksoft/drewlabs-php-contracts.git', $library->getRepository()[0]->getURL());
    }

    public function test_repository_select_library_by_id_returns_instance_library_interface()
    {
        $instance = $this->createYMLBasedRepository();
        /**
         * @var WebServiceLibraryConfigInterface|AuthBasedLibraryConfigInterface
         */
        $library = $instance->select('63ba9864-ce73-4421-8313-8af72df96107');
        $this->assertInstanceOf(WebServiceLibraryConfigInterface::class, $library);
        $this->assertSame('https://gtpsecurecard.com/', $library->getHost());
        $this->assertInstanceOf(AuthCredentialsInterface::class, $library->getAuth());
        $this->assertSame('ayael', $library->getAuth()->id());
        $this->assertSame('SuperSecret', $library->getAuth()->secret());
    }

    public function test_repository_select_library_by_id_returns_null_if_library_not_exists()
    {
        $instance = $this->createYMLBasedRepository();
        $library = $instance->select('test/hello-world');
        $this->assertNull($library);
    }

    public function test_repository_add_library_add_library_to_the_list_of_libraries()
    {
        $instance = $this->createYMLBasedRepository(false);
        $instance->add(LibraryConfig::new('TestLib', 'composer', 'test/library', TestLibraryFactory::class));
        $library = $instance->select('test/library');
        $this->assertInstanceOf(LibraryConfigInterface::class, $library);
        $this->assertSame('TestLib', $library->getName());
    }

    public function test_repository_select_all_returns_an_iteratble()
    {
        $instance = $this->createYMLBasedRepository(false);
        $result = $instance->selectAll(static function ($item) {
            return 'composer' === $item->type;
        });
        $this->assertInstanceOf(\Traversable::class, $result);
        $this->assertSame(2, iterator_count($result));
    }

    private function createYMLBasedRepository(bool $persitable = true)
    {
        return new InMemoryConfigurationRepository(YAMLDefinitionsProvider::create(realpath(__DIR__.'/Stubs'), $persitable));
    }
}

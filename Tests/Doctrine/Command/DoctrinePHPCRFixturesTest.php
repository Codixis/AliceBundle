<?php

/*
 * This file is part of the Hautelook\AliceBundle package.
 *
 * (c) Baldur Rensch <brensch@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hautelook\AliceBundle\Tests\Doctrine\Command;

use Doctrine\ODM\PHPCR\DocumentManager;
use Hautelook\AliceBundle\Tests\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Baldur Rensch <brensch@gmail.com>
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class DoctrinePHPCRFixturesTest extends KernelTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->application->add(
            self::$kernel->getContainer()->get('hautelook_alice.doctrine.phpcr.command.load_command')
        );
    }

    public function testFixturesLoading()
    {
        $this->markTestSkipped();
        $command = $this->application->find('hautelook_alice:doctrine:phpcr:fixtures:load');

        $commandTester = new CommandTester($command);
        $commandTester->execute([], ['interactive' => false]);

        $this->verifyProducts();
    }

    /**
     * @dataProvider loadCommandProvider
     *
     * @param array  $inputs
     * @param string $expected
     */
    public function testFixturesRegistering(array $inputs, $expected)
    {
        $this->markTestSkipped();
        $command = $this->application->find('hautelook_alice:doctrine:phpcr:fixtures:load');

        $commandTester = new CommandTester($command);
        $commandTester->execute($inputs, ['interactive' => false]);

        $this->assertEquals(trim($expected,' '), trim($commandTester->getDisplay(), ' '));
    }

    private function verifyProducts()
    {
        $tasks = $this->doctrineManager->getRepository
        ('\Hautelook\AliceBundle\Tests\SymfonyApp\TestBundle\Document\Task')->findAll();

        $this->assertCount(10, $tasks);
    }

    public function loadCommandProvider()
    {
        $data = [];

        $data[] = [
            [],
            <<<EOF
              > fixtures found:
      - /home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/DataFixtures/PHPCR/task.yml
  > purging database
  > fixtures loaded

EOF
        ];

        // Fix paths
        foreach ($data as $index => $dataSet) {
            $data[$index][1] = str_replace('/home/travis/build/theofidry/AliceBundle', getcwd(), $dataSet[1]);
        }

        return $data;
    }
}

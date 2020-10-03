<?php

declare(strict_types=1);

namespace Neos\StarterSmokeTester\Command;

use GuzzleHttp\Psr7\Uri;
use Neos\Flow\Cli\CommandController;
use Neos\Starter\Api\Configuration;
use Neos\Starter\Generator\Generator;
use Neos\StarterSmokeTester\Crawler\CrawlerSubscriber;
use Neos\Utility\Files;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Process\Process;
use Terminal42\Escargot\BaseUriCollection;
use Terminal42\Escargot\Escargot;
use Terminal42\Escargot\Queue\InMemoryQueue;
use Terminal42\Escargot\Subscriber\HtmlCrawlerSubscriber;

class SmokeTestCommandController extends CommandController
{

    /**
     * @param string $fullManifestFile
     * @param string $baseOutputFolder
     */
    public function runCommand(string $fullManifestFile, string $baseOutputFolder)
    {
        $alreadyTestedFeatureHashes = [];

        $workdir = getcwd();
        while (true) {
            chdir($workdir);
            $manifestFileContents = json_decode(file_get_contents($fullManifestFile), true);

            foreach ($manifestFileContents['features'] as $i => $feature) {
                if ($feature['name'] === 'Neos') {
                    // the Neos feature is always active.
                    continue;
                }

                if (rand(0, 1) === 0) {
                    // disable features randomly
                    unset($manifestFileContents['features'][$i]);
                }
            }

            $manifestHash = sha1(json_encode($manifestFileContents));
            if (isset($alreadyTestedFeatureHashes[$manifestHash])) {
                $this->outputLine('Skipping ' . $manifestHash . ' (already done)');
                continue;
            }

            $this->outputLine($manifestHash);
            $outputFolder = Files::concatenatePaths([$baseOutputFolder, $manifestHash]);
            Files::createDirectoryRecursively($outputFolder);

            file_put_contents(Files::concatenatePaths([$outputFolder, 'manifest.json']), json_encode($manifestFileContents, JSON_PRETTY_PRINT));

            $configuration = Configuration::fromArray($manifestFileContents);
            $this->outputLine(' - starting generation.');
            $generator = new Generator($configuration);
            $result = $generator->generate();
            $outputFolderDistDir = Files::concatenatePaths([$outputFolder, 'dist']);
            $result->writeToFolder($outputFolderDistDir);

            $this->outputLine(' - running composer install.');

            $process = new Process(['composer', 'install'], $outputFolderDistDir);
            $process->run();

            file_put_contents(Files::concatenatePaths([$outputFolder, '01-composer-stdout.log']), $process->getOutput());
            file_put_contents(Files::concatenatePaths([$outputFolder, '01-composer-stderr.log']), $process->getErrorOutput());

            if (!$process->isSuccessful()) {
                $this->outputLine('   !!! composer process failed. check log.');
                continue;
            }
            $this->outputLine(' - starting flow server:run');

            // TODO: DB server!
            $process = new Process(['./flow', 'server:run'], $outputFolderDistDir);
            $process->start();
            $process->waitUntil(function ($type, $output) {
                var_dump($output);
                return str_contains($output, 'Server running');
            });
            if (!$process->isRunning()) {
                $this->outputLine('!!! ERROR - Flow process was terminated.');
                continue;
            }

            $this->outputLine(' - starting smoke tests');

            $this->smokeTestSite($outputFolder);

            $process->stop();
        }
    }

    protected function smokeTestSite($outputFolder)
    {
        sleep(1);
        $baseUris = new BaseUriCollection();
        $baseUris->add(new Uri('http://127.0.0.1:8081'));
        $queue = new InMemoryQueue();

        $escargot = Escargot::create($baseUris, $queue);
        $escargot->addSubscriber(new HtmlCrawlerSubscriber());
        $escargot->addSubscriber(new CrawlerSubscriber());
        $escargot->withLogger(new ConsoleLogger($this->output->getOutput()));
        $escargot->crawl();

        $this->outputLine('  Finished crawling.');
    }
}

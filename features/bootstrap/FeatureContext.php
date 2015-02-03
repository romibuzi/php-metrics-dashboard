<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use PHPUnit_Framework_Assert as Assert;
use Symfony\Component\Console\Tester\CommandTester;

class FeatureContext implements Context
{
    /**
     * @var Silex\Application
     */
    private $app;
    /**
     * @var Symfony\Component\HttpKernel\Client
     */
    private $client;
    /**
     * @var Symfony\Component\Console\Application
     */
    private $console;
    /**
     * @var CommandTester
     */
    private $tester;

    /**
     * @BeforeScenario
     */
    public function setup()
    {
        putenv('ENV=test');
        $app = require __DIR__ . '/../../app/app.php';
        $console = require __DIR__ . '/../../app/console.php';

        $this->app = $app;
        $this->console = $console;

        $this->client = new Symfony\Component\HttpKernel\Client($this->app);
    }

    /**
     * @AfterScenario
     *
     * Clean up things
     */
    public function tearDown()
    {
        if (file_exists($this->app['projects.config_file'])) {
            unlink($this->app['projects.config_file']);
        }

        if (false !== realpath($this->app['projects.report_folder'])) {
            exec('rm -rf ' . escapeshellarg($this->app['projects.report_folder']));
        }

        if (false !== realpath($this->app['projects.report_folder'])) {
            exec('rm -rf ' . escapeshellarg($this->app['projects.source_folder']));
        }
    }

    /**
     * @Given I am in the root directory
     */
    public function iAmInTheRootDirectory()
    {
        chdir($this->app['root_dir']);
    }

    /**
     * @Given I have a config file with content:
     */
    public function iHaveAConfigFiledWithContent(PyStringNode $content)
    {
        file_put_contents($this->app['projects.config_file'], (string) $content);

        Assert::assertEquals((string) $content, file_get_contents($this->app['projects.config_file']));
    }

    /**
     * @When I run :command command
     */
    public function iRunCommand($name)
    {
        $command = $this->console->find($name);

        $this->tester = new CommandTester($command);
        $returnCode = $this->tester->execute(['command' => $command->getName()]);

        Assert::assertEquals(0, $returnCode);
    }

    /**
     * @Then I should get :name directory in projects sources folder
     */
    public function iShouldGetProjectDirectoryInSourceFolder($dir)
    {
        Assert::assertContains($dir, scandir($this->app['projects.source_folder']));
    }

    /**
     * @Then :name sources folder should be a git repository
     */
    public function projectSourceFolderShouldBeAGitRepository($dir)
    {
        $dir = $this->app['projects.source_folder'] . $dir;

        Assert::assertEquals('true', exec("cd {$dir} && git rev-parse --is-inside-work-tree"));
    }

    /**
     * @Then I should get :name directory in projects reports folder
     */
    public function iShouldGetProjectDirectoryInReportsFolder($dir)
    {
        Assert::assertContains($dir, scandir($this->app['projects.report_folder']));
    }

    /**
     * @Then I should get a report file in :name reports folder
     */
    public function iShouldGetReportsInProjectReportsFolder($dir)
    {
        Assert::assertNotEmpty(scandir($this->app['projects.report_folder'] . $dir));
    }

    /**
     * @When I call :method :url
     */
    public function callUrlWithMethod($method, $endpoint)
    {
        $this->client->request($method, $endpoint);
    }

    /**
     * @Then The response code should be :code
     */
    public function responseCodeShouldBe($statusCode)
    {
        Assert::assertEquals($statusCode, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @Given The reponse should contains :content
     */
    public function responseShouldContains($content)
    {
        Assert::assertContains($content, $this->client->getResponse()->getContent());
    }

    /**
     * @Given The reponse should contains a report link for the actual date
     */
    public function responseShouldContainsAReportForTheActualDate()
    {
        $date = (new \DateTime('now'))->format('d-m-Y');

        Assert::assertContains($date, $this->client->getResponse()->getContent());
    }
}

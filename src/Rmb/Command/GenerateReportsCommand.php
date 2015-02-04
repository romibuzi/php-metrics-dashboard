<?php

namespace Rmb\Command;

use GitWrapper\Event\GitLoggerListener;
use GitWrapper\GitWrapper;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

/**
 * Class GenerateReportsCommand
 *
 * @package Rmb\Command
 */
class GenerateReportsCommand extends Command
{
    /** @var Filesystem $fs */
    protected $fs;
    /** @var LoggerInterface */
    protected $logger;
    /** @var GitWrapper */
    protected $wrapper;

    /** @var string */
    protected $phpmetricsExecutable;
    /** @var string */
    protected $projectsConfigFile;
    /** @var string */
    protected $projectsSourceFolder;
    /** @var string */
    protected $projectsReportsFolder;

    public function __construct(FileSystem $fs, GitWrapper $wrapper, LoggerInterface $logger)
    {
        parent::__construct();

        $this->fs = $fs;
        $this->logger = $logger;
        $this->wrapper = $wrapper;
        $this->wrapper->addLoggerListener(new GitLoggerListener($this->logger));
        $this->wrapper->setTimeout(600); // Increase the timeout for big git project
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('generate-reports')
            ->setDescription('Generate PhpMetrics for all projects defined in projects.json')
        ;
    }

    /**
     * @param string $executablePath
     *
     * @return $this
     */
    public function setPhpMetricsExecutablePath($executablePath)
    {
        $this->phpmetricsExecutable = $executablePath;

        return $this;
    }

    /**
     * @param string $file
     *
     * @return $this
     */
    public function setProjectsConfigFile($file)
    {
        $this->projectsConfigFile = $file;

        return $this;
    }

    /**
     * @param string $folder
     *
     * @return $this
     */
    public function setProjectsSourceFolder($folder)
    {
        $this->projectsSourceFolder = $folder;

        return $this;
    }

    /**
     * @param string $folder
     *
     * @return $this
     */
    public function setProjectsReportsFolder($folder)
    {
        $this->projectsReportsFolder = $folder;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (! $this->fs->exists($this->projectsConfigFile)) {
            throw new FileNotFoundException(null, 0, null, $this->projectsConfigFile);
        }

        $projects = json_decode(file_get_contents($this->projectsConfigFile), true);

        foreach ($projects as $projectName => $projectConfig) {
            $projectSourceFolder  = $this->projectsSourceFolder . $projectName;
            $projectReportsDir = $this->projectsReportsFolder . $projectName;

            // Clone or pull different projects inside ROOT_DIR/var/projects directory
            if (! $this->fs->exists($projectSourceFolder)) {
                $this->fs->mkdir($projectSourceFolder);

                // Create phpmetrics reports directory for the project too
                $this->fs->mkdir($projectReportsDir);

                $output->writeln("<info>Cloning {$projectName} into {$projectSourceFolder} ...</info>");

                $git = $this->wrapper->cloneRepository($projectConfig['url'], $projectSourceFolder);

            } else {
                // Project was already initialized, so just pull the latest changes
                $output->writeln("<info>Pulling latest changes of {$projectName} ...</info>");

                $git = $this->wrapper->init($projectSourceFolder);
                $git->pull();
            }

            // Allow to pull specific branch of the project
            if (isset($projectConfig['branch'])) {
                $git->checkout($projectConfig['branch']);
            }

            // Then run phpmetrics tools on theese projects
            $output->writeln("<info>Generate a new PhpMetrics report for {$projectName} ...</info>");

            $reportFilePath = $projectReportsDir . '/' . (new \DateTime())->format('d-m-Y');

            $arguments =  ' --report-html=' . $reportFilePath . ' ' . $projectSourceFolder;

            if (isset($projectConfig['excluded-dirs'])
                && is_array($projectConfig['excluded-dirs'])
            ) {
                $arguments .= ' --excluded-dirs=' . implode(',', $projectConfig['excluded-dirs']);
            }

            $process = new Process('php ' . $this->phpmetricsExecutable . $arguments, $this->projectsSourceFolder);
            $process->setTimeout(600); // Increase the timeout for big git project
            $process->run();
            if (! $process->isSuccessful()) {
                $message = "Following error happened during phpmetrics execution : {$process->getErrorOutput()}";

                $this->logger->error($message);
                $output->writeln("<error>{$message}</error>");

                break;
            }

            $output->writeln("<info>Sucessfully generated a PhpMetrics report for {$projectName}!</info>");
        }

        $output->writeln("<info>Done!</info>");
    }
}

<?php

namespace Rmb\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\Response;

/**
 * DefaultController
 *
 * @package Rmb\Controller
 */
class DefaultController
{
    /** @var string */
    private $projectsFolder;
    /** @var \Twig_Environment */
    private $twig;
    /** @var LoggerInterface */
    private $logger;

    public function __construct($projectsFolder, \Twig_Environment $twig, LoggerInterface $logger)
    {
        $this->projectsFolder = $projectsFolder;

        $this->twig = $twig;
        $this->logger = $logger;
    }

    /**
     * Main Action, list all differents projects
     *
     * @return string : the template
     */
    public function indexAction()
    {
        $finder = (new Finder())->directories()->in($this->projectsFolder);

        return $this->twig->render('index.twig', ['projects' => $finder]);
    }

    /**
     * List reports of a project
     *
     * @param string $project
     *
     * @return string : the template or 404 if the project is not found
     */
    public function listReportsAction($project)
    {
        try {
            $finder = (new Finder())->files()->in($this->projectsFolder . $project);

            $reports = [];
            /** @var \Symfony\Component\Finder\SplFileInfo $file */
            foreach ($finder as $file) {
                $reports[] = new \DateTime($file->getRelativePathname());
            }

            // Reverse the array to get the most recent reports in first
            return $this->twig->render('project.twig', ['project' => $project, 'reports' => array_reverse($reports)]);
        } catch (\InvalidArgumentException $e) {
            $this->logger->error($e->getMessage());

            return new Response('Project not found !', 404);
        }
    }

    /**
     * Display a report of a project
     *
     * @param string $project
     * @param string $report
     *
     * @return string : the report or 404 if the report is not found
     */
    public function displayReportsAction($project, $report)
    {
        $path = $this->projectsFolder . $project . '/' . $report;

        try {
            $reportContent = (new SplFileInfo($path, '', ''))->getContents();

            return $this->twig->render('report.twig', ['project' => $project, 'report' => $reportContent]);
        } catch (\RuntimeException $e) {
            $this->logger->error($e->getMessage());

            return new Response('Report not found !', 404);
        }
    }
}

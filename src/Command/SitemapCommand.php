<?php
namespace Snowdog\DevTest\Command;

use Snowdog\DevTest\Model\PageManager;
use Snowdog\DevTest\Model\WebsiteManager;
use Snowdog\DevTest\Model\UserManager;
use Symfony\Component\Console\Output\OutputInterface;
use Zbigniewkuras\XmlParser\SitemapParser;

class SitemapCommand
{

    /**
     *
     * @var WebsiteManager
     */
    private $websiteManager;

    /**
     *
     * @var PageManager
     */
    private $pageManager;

    /**
     *
     * @var UserManager
     */
    private $userManager;

    /**
     *
     * @var SitemapParser
     */
    private $sitemapParser;

    /**
     * 
     * @param WebsiteManager $websiteManager
     * @param PageManager $pageManager
     * @param UserManager $userManager
     * @param SitemapParser $sitemapParser
     */
    public function __construct(WebsiteManager $websiteManager, PageManager $pageManager, UserManager $userManager, SitemapParser $sitemapParser)
    {
        $this->websiteManager = $websiteManager;
        $this->pageManager = $pageManager;
        $this->sitemapParser = $sitemapParser;
        $this->userManager = $userManager;
    }

    /**
     *
     * @param string $sitemapfile
     * @param string $login
     * @param OutputInterface $output
     */
    public function __invoke($sitemapfile, $login, OutputInterface $output)
    {
        if (! file_exists($sitemapfile)) {
            $output->writeln('<error>Sitemap ' . $sitemapfile . ' does not exists!</error>');
            return;
        }
        $user = $this->userManager->getByLogin($login);
        if (! $user) {
            $output->writeln('<error>User with login ' . $login . ' does not exists!</error>');
            return;
        }

        $data = file_get_contents($sitemapfile);
        $data = $this->sitemapParser->setRawData($data)->parseData();
        if ($data) {
            foreach ($data as $hostname => $paths) {
                $websiteId = $this->websiteManager->create($user, $hostname, $hostname);
                if (! $websiteId) {
                    $output->writeln('<error>Website with hostname ' . $hostname . ' has not been added!</error>');
                    continue;
                }
                $output->writeln('<info>Added website ' . $hostname . '</info>');
                $website = $this->websiteManager->getById($websiteId);
                foreach ($paths as $path) {
                    $this->pageManager->create($website, $path);
                    $output->writeln('<info>Added page ' . $hostname . $path . '</info>');
                }
            }
        } else {
            $output->writeln('<error>No data to process!</error>');
        }
    }
}
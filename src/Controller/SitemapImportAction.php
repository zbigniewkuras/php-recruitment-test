<?php
namespace Snowdog\DevTest\Controller;

use Snowdog\DevTest\Model\PageManager;
use Snowdog\DevTest\Model\UserManager;
use Snowdog\DevTest\Model\Website;
use Snowdog\DevTest\Model\WebsiteManager;
use Zbigniewkuras\XmlParser\SitemapParser;

class SitemapImportAction
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
     * @var Website
     */
    private $website;

    /**
     *
     * @var SitemapParser
     */
    private $sitemapParser;

    /**
     *
     * @param UserManager $userManager
     * @param WebsiteManager $websiteManager
     * @param PageManager $pageManager
     * @param SitemapParser $sitemapParser
     */
    public function __construct(UserManager $userManager, WebsiteManager $websiteManager, PageManager $pageManager, SitemapParser $sitemapParser)
    {
        $this->websiteManager = $websiteManager;
        $this->pageManager = $pageManager;
        $this->userManager = $userManager;
        $this->sitemapParser = $sitemapParser;
    }

    public function execute()
    {
        if (isset($_FILES['sitemap'])) {
            $errors = [];

            $fileTmp = $_FILES['sitemap']['tmp_name'];
            $fileType = $_FILES['sitemap']['type'];

            $ext = strtolower(pathinfo($_FILES['sitemap']['name'], PATHINFO_EXTENSION));
            $allowedExtensions = [
                'xml'
            ];
            $allowedMimes = [
                'text/xml'
            ];

            if (! in_array($ext, $allowedExtensions)) {
                $errors[] = 'Allowed extensions: ' . implode(',', $allowedExtensions);
            }
            if (! in_array($fileType, $allowedMimes)) {
                $errors[] = 'Allowed MIME: ' . implode(',', $allowedMimes);
            }

            if (! $errors) {
                $user = $this->userManager->getByLogin($_SESSION['login']);

                $data = file_get_contents($fileTmp);
                $data = $this->sitemapParser->setRawData($data)->parseData();
                if ($data) {
                    foreach ($data as $hostname => $paths) {
                        $websiteId = $this->websiteManager->create($user, $hostname, $hostname);
                        if (! $websiteId) {
                            $errors[] = 'Website with hostname ' . $hostname . ' has not been added!';
                            continue;
                        }
                        $website = $this->websiteManager->getById($websiteId);
                        foreach ($paths as $path) {
                            $this->pageManager->create($website, $path);
                        }
                    }
                } else {
                    $errors[] = 'No data to process!';
                }
            }
            if ($errors) {
                $_SESSION['flash'] = implode('</br>', $errors);
            }
        }

        header('Location: /');
    }
}
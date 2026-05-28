<?php
namespace HelloWorld\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class HelloController extends AbstractActionController
{
    public function greetAction()
    {
        $name = $this->params()->fromQuery('name', 'Stranger');

        // Read a SITE setting
        // 'Omeka\Settings\Site' is scoped to the *current* site.
        // Service: 'Omeka\Settings\Site'  →  table: `site_setting`
        $sm = $this->getEvent()->getApplication()->getServiceManager();
        $siteSettings = $sm->get('Omeka\Settings\Site');
        $greeting = $siteSettings->get('helloworld_site_greeting', 'Bon giorno');

        return new ViewModel([
            'name' => $name,
            'greetingFromSiteSettings' => $greeting
        ]);
    }

    public function greetAdminAction()
    {
        $sm = $this->getEvent()->getApplication()->getServiceManager();

        // Read a GLOBAL setting (same value for every Omeka S site).
        // Service: 'Omeka\Settings'  →  table: `setting`
        $settings = $sm->get('Omeka\Settings');
        $name = $settings->get('helloworld_name', 'Placeholder');

        // Read a SITE setting
        // 'Omeka\Settings\Site' is scoped to the *current* site.
        // Service: 'Omeka\Settings\Site'  →  table: `site_setting`
        $siteSettings = $sm->get('Omeka\Settings\Site');
        $greeting = $siteSettings->get('helloworld_site_greeting', 'Bon giorno');

        return new ViewModel([
            'nameFromSettings' => $name,
            'greetingFromSiteSettings' => $greeting
        ]);
    }

    public function indexAction()
    {
        $sm = $this->getEvent()->getApplication()->getServiceManager();

        // --- Reading GLOBAL settings -------------------------------------------
        // 'Omeka\Settings' is shared across all sites.
        $globalSettings = $sm->get('Omeka\Settings');
        $globalName     = $globalSettings->get('helloworld_name', 'World');

        // --- Reading SITE settings ---------------------------------------------
        // 'Omeka\Settings\Site' is scoped to the *current* site.
        // Omeka S automatically targets the correct site when a public-facing
        // site route is active (i.e. the visitor is browsing a site).
        $siteSettings = $sm->get('Omeka\Settings\Site');
        $siteGreeting = $siteSettings->get('helloworld_site_greeting', 'Hello');
        $showWeather  = (bool) $siteSettings->get('helloworld_site_show_weather', '0');

        // Pass both to the view so you can see the difference.
        return new ViewModel([
            'globalName'   => $globalName,
            'siteGreeting' => $siteGreeting,
            'showWeather'  => $showWeather,
        ]);
    }
}

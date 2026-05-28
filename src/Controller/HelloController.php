<?php
namespace HelloWorld\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class HelloController extends AbstractActionController
{
    public function greetAction()
    {
        $name = $this->params()->fromQuery('name', 'Stranger');
        return new ViewModel(['name' => $name]);
    }

    public function greetAdminAction()
    {
        // Read a GLOBAL setting (same value for every Omeka S site).
        // Service: 'Omeka\Settings'  →  table: `setting`
        $settings = $this->getEvent()->getApplication()->getServiceManager()->get('Omeka\Settings');
        $name = $settings->get('helloworld_name', 'Placeholder');
        return new ViewModel(['nameFromSettings' => $name]);
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

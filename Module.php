<?php

namespace HelloWorld;

use Omeka\Module\AbstractModule;
use Laminas\Mvc\Controller\AbstractController;
use Laminas\View\Renderer\PhpRenderer;
use Laminas\EventManager\SharedEventManagerInterface;
use Laminas\EventManager\Event;
use Laminas\Form\Element;

class Module extends AbstractModule
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    // -------------------------------------------------------------------------
    // GLOBAL MODULE SETTINGS
    // Stored in the `setting` table. Same value for every Omeka S site.
    // Configured at: Admin → Modules → Hello World (Configure)
    // -------------------------------------------------------------------------

    /**
     * Renders HTML form fields shown on the module's global settings page.
     */
    public function getConfigForm(PhpRenderer $renderer)
    {
        // Service: 'Omeka\Settings'  →  table: `setting`
        $settings = $this->getServiceLocator()->get('Omeka\Settings');
        $name     = $settings->get('helloworld_name', '');

        return sprintf(
            '<label for="helloworld_name">Default name (global):</label>
            <input id="helloworld_name" name="helloworld_name" value="%s" />',
            htmlspecialchars((string) $name, ENT_QUOTES, 'UTF-8')
        );
    }

    /**
     * Reads POST data and writes it to the global `setting` table.
     */
    public function handleConfigForm(AbstractController $controller)
    {
        $name     = $controller->getRequest()->getPost('helloworld_name', '');
        $settings = $this->getServiceLocator()->get('Omeka\Settings');
        $settings->set('helloworld_name', $name);

        $controller->messenger()->addSuccess('Global name saved: ' . htmlspecialchars($name));
        return true;
    }

    // -------------------------------------------------------------------------
    // SITE SETTINGS
    // Stored in the `site_setting` table. Each site has its own value.
    // Configured at: Admin → Sites → [Your Site] → Settings
    //
    // How it works:
    //   1. attachListeners()               – registers hooks on SiteSettingsForm
    //   2. addSiteSettingsFormElements()   – adds fields when the page renders
    //   3. addSiteSettingsInputFilters()   – adds validation on form submit
    //
    // Saving is handled AUTOMATICALLY by the Omeka S core SiteAdmin controller,
    // which iterates the flat form data and calls:
    //   foreach ($formData as $key => $value) { $siteSettings->set($key, $value); }
    //
    // IMPORTANT: elements must be added directly to the FORM ROOT (not inside
    // a Fieldset), so the core save loop sees them as flat key→value pairs.
    // -------------------------------------------------------------------------

    /**
     * Register event listeners. Called automatically by AbstractModule at boot.
     */
    public function attachListeners(SharedEventManagerInterface $sharedEventManager): void
    {
        $sharedEventManager->attach(
            \Omeka\Form\SiteSettingsForm::class,
            'form.add_elements',       // fires when Omeka S renders the settings page
            [$this, 'addSiteSettingsFormElements']
        );

        $sharedEventManager->attach(
            \Omeka\Form\SiteSettingsForm::class,
            'form.add_input_filters',  // fires before validation on form submit
            [$this, 'addSiteSettingsInputFilters']
        );
    }

    /**
     * Add Hello World fields to the Site Settings form.
     *
     * The correct service name is 'Omeka\Settings\Site' (NOT SiteSettings).
     * The current site is already set on this service by the admin router, so
     * ->get() / ->set() automatically target the site being edited.
     */
    public function addSiteSettingsFormElements(Event $event): void
    {
        // Service: 'Omeka\Settings\Site'  →  table: `site_setting`
        $siteSettings = $this->getServiceLocator()->get('Omeka\Settings\Site');

        /** @var \Omeka\Form\SiteSettingsForm $form */
        $form = $event->getTarget();

        // --- Field 1: text input -----------------------------------------------
        // Add the element directly to the form root (NOT inside a Fieldset).
        // The 'name' here becomes the key used by the core save loop.
        $form->add([
            'name'    => 'helloworld_site_greeting',
            'type'    => Element\Text::class,
            'options' => [
                'label' => 'Hello World: site greeting', // @translate
                'info'  => 'Greeting shown to visitors on this specific site.',
            ],
            'attributes' => [
                'id'    => 'helloworld_site_greeting',
                // Pre-populate with the currently saved value (default: 'Hello').
                'value' => $siteSettings->get('helloworld_site_greeting', 'Hello'),
            ],
        ]);

        // --- Field 2: checkbox -------------------------------------------------
        $form->add([
            'name'    => 'helloworld_site_show_weather',
            'type'    => Element\Checkbox::class,
            'options' => [
                'label'              => 'Hello World: show weather widget', // @translate
                'info'               => 'Display the weather widget on this site.',
                'use_hidden_element' => true,  // ensures a value posts even when unchecked
                'checked_value'      => '1',
                'unchecked_value'    => '0',
            ],
            'attributes' => [
                'id'    => 'helloworld_site_show_weather',
                'value' => $siteSettings->get('helloworld_site_show_weather', '0'),
            ],
        ]);
    }

    /**
     * Add input filters / validation for the site settings fields.
     * Filters are added to the FORM-LEVEL input filter (not a fieldset).
     */
    public function addSiteSettingsInputFilters(Event $event): void
    {
        /** @var \Laminas\InputFilter\InputFilter $inputFilter */
        $inputFilter = $event->getParam('inputFilter');

        $inputFilter->add([
            'name'     => 'helloworld_site_greeting',
            'required' => false,
            'filters'  => [
                ['name' => \Laminas\Filter\StringTrim::class],
                ['name' => \Laminas\Filter\StripTags::class],
            ],
        ]);

        $inputFilter->add([
            'name'     => 'helloworld_site_show_weather',
            'required' => false,
        ]);
    }
}

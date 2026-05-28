<?php

namespace HelloWorld;

use HelloWorld\Form\SiteSettingsFieldset;
use Omeka\Module\AbstractModule;
use Laminas\Mvc\Controller\AbstractController;
use Laminas\View\Renderer\PhpRenderer;
use Laminas\EventManager\SharedEventManagerInterface;
use Laminas\EventManager\Event;

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
    // Pattern (inspired by Daniel-KM/BlockPlus):
    //   1. SiteSettingsFieldset (src/Form/SiteSettingsFieldset.php)
    //      – declares all form elements and element_groups; no DB access.
    //   2. attachListeners()
    //      – registers event hooks on SiteSettingsForm.
    //   3. addSiteSettingsFormElements()  [form.add_elements event]
    //      – creates the fieldset via FormElementManager (which calls init()),
    //        merges element_groups into the parent form,
    //        reads current values from 'Omeka\Settings\Site',
    //        adds each element to the form ROOT (required for the core save loop),
    //        and calls populateValues() to fill them in.
    //   4. addSiteSettingsInputFilters()  [form.add_input_filters event]
    //      – attaches validation/filter rules at the form root level.
    //
    // Saving is handled AUTOMATICALLY by the Omeka S core SiteAdmin controller:
    //   foreach ($formData as $key => $value) { $siteSettings->set($key, $value); }
    // -------------------------------------------------------------------------

    public function attachListeners(SharedEventManagerInterface $sharedEventManager): void
    {
        $sharedEventManager->attach(
            \Omeka\Form\SiteSettingsForm::class,
            'form.add_elements',
            [$this, 'addSiteSettingsFormElements']
        );
        $sharedEventManager->attach(
            \Omeka\Form\SiteSettingsForm::class,
            'form.add_input_filters',
            [$this, 'addSiteSettingsInputFilters']
        );
    }

    /**
     * Add Hello World fields to the Site Settings form.
     *
     * Steps:
     *  1. Get the fieldset from FormElementManager (triggers init() automatically).
     *  2. Merge our element_groups into the parent form so they appear as tabs/sections.
     *  3. Read the currently stored values from 'Omeka\Settings\Site'.
     *  4. Add each element to the form ROOT (not inside the fieldset wrapper),
     *     because the Omeka S core save loop iterates flat root-level data:
     *       foreach ($formData as $key => $value) { $siteSettings->set($key, $value); }
     *  5. Call populateValues() to set the current values on those elements.
     */
    public function addSiteSettingsFormElements(Event $event): void
    {
        $services           = $this->getServiceLocator();
        $formElementManager = $services->get('FormElementManager');
        // Service: 'Omeka\Settings\Site'  →  table: `site_setting`
        $siteSettings       = $services->get('Omeka\Settings\Site');

        /** @var \Omeka\Form\SiteSettingsForm $form */
        $form = $event->getTarget();

        // 1. Create fieldset via FormElementManager so init() is called automatically.
        /** @var SiteSettingsFieldset $fieldset */
        $fieldset = $formElementManager->get(SiteSettingsFieldset::class);

        // 2. Merge our element groups into the form so they render as sections/tabs.
        $fieldsetGroups = $fieldset->getOption('element_groups') ?: [];
        $form->setOption('element_groups', array_merge(
            $form->getOption('element_groups') ?: [],
            $fieldsetGroups
        ));

        // 3. Read all current values from the site_setting table.
        $data = [
            'helloworld_site_greeting'     => $siteSettings->get('helloworld_site_greeting', 'Hello'),
            'helloworld_site_show_weather' => $siteSettings->get('helloworld_site_show_weather', '0'),
        ];

        // 4. Add each element from the fieldset directly to the form root.
        //    Using the fieldset as a structural/organisational helper only.
        foreach ($fieldset->getElements() as $element) {
            $form->add($element);
        }

        // 5. Populate the form elements with the stored values.
        $form->populateValues($data);
    }

    /**
     * Add input filters / validation rules at the form root level.
     * The 'inputFilter' param is the form's own InputFilter instance.
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

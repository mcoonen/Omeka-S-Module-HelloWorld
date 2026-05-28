<?php
declare(strict_types=1);

namespace HelloWorld\Form;

use Laminas\Form\Element;
use Laminas\Form\Fieldset;

/**
 * Defines the Hello World fields that appear on the
 * Admin → Sites → [site] → Settings page.
 *
 * This class only DECLARES elements; it never reads or writes the database.
 * Values are populated by Module::addSiteSettingsFormElements() after the
 * fieldset is created, so the elements here have no 'value' attribute set.
 *
 * Following the pattern of Daniel-KM's BlockPlus module:
 * @see https://github.com/Daniel-KM/Omeka-S-module-BlockPlus/blob/master/src/Form/SiteSettingsFieldset.php
 */
class SiteSettingsFieldset extends Fieldset
{
    /**
     * Label shown as the fieldset heading (used by some themes/admin UIs).
     */
    protected $label = 'Hello World';

    /**
     * Element groups to register on the parent SiteSettingsForm.
     * Keys become group identifiers; values are the human-readable tab/section label.
     * Each element below references one of these keys via its 'element_group' option.
     */
    protected $elementGroups = [
        'helloworld' => 'Hello World', // @translate
    ];

    public function init(): void
    {
        $this
            ->setAttribute('id', 'hello-world')
            ->setOption('element_groups', $this->elementGroups)

            // ---- Field 1: text input ----------------------------------------
            ->add([
                'name'    => 'helloworld_site_greeting',
                'type'    => Element\Text::class,
                'options' => [
                    'element_group' => 'helloworld',           // which tab/group to display in
                    'label'         => 'Site greeting',        // @translate
                    'info'          => 'Greeting message shown to visitors on this specific site.',
                ],
                'attributes' => [
                    'id' => 'helloworld_site_greeting',
                    // No 'value' here — it is set via populateValues() in Module.php
                ],
            ])

            // ---- Field 2: checkbox ------------------------------------------
            ->add([
                'name'    => 'helloworld_site_show_weather',
                'type'    => Element\Checkbox::class,
                'options' => [
                    'element_group'      => 'helloworld',
                    'label'              => 'Show weather widget', // @translate
                    'info'               => 'Display the weather widget on this site.',
                    'use_hidden_element' => true,  // posts a value even when unchecked
                    'checked_value'      => '1',
                    'unchecked_value'    => '0',
                ],
                'attributes' => [
                    'id' => 'helloworld_site_show_weather',
                ],
            ])
        ;
    }
}


<?php

namespace aetchell\Elemental\Blocks {

    use SilverStripe\Forms\CheckboxField;
    use SilverStripe\Forms\CheckboxSetField;
    use SilverStripe\Forms\DropdownField;
    use SilverStripe\Forms\FieldList;
    use SilverStripe\ORM\DataExtension;
    use Symbiote\MultiValueField\Fields\MultiValueTextField;

    class HighchartConfigExtension extends DataExtension {

        private static $db = [
            'HighchartLibraryAllPages' => 'Boolean',
            'HighchartLibs' => 'Varchar(255)',
            'HighchartColours' => 'MultiValueField',
            'HighchartAdditionalLibs' => 'MultiValueField'
        ];

        private static $defaults = [
            'HighchartLibs' => 'stock'
        ];

        public function updateCMSFields(FieldList $fields) {

            $HighchartAllPages = CheckboxField::create('HighchartLibraryAllPages', 'Use Highcharts libraries on all pages?');
            $HighchartAllPages->setDescription('Do you want to include the libraries on all pages or just the ones that use the Highcharts block? This is useful if you use other Highcharts integrations on your site.');

            $HighchartLibs = CheckboxSetField::create('HighchartLibs', 'Which libraries to include', [
                'chart' => 'Highcharts',
                'stock' => 'Highcharts Stock',
                // coming soon...
                //'maps' => 'Highcharts Maps',
                //'gantt' => 'Highcharts Gantt'
            ]);
            $HighchartLibs->setDescription('Which libraries do you want to include on all pages? Note that currently, highstock is added regardless of your choice above. Highstock contains all the highcharts functionality.');

            $HighchartMoreLibs = MultiValueTextField::create('HighchartAdditionalLibs', 'Additional libraries')
                ->setAttribute('placeholder','modules/solid-gauge.js');
            $HighchartMoreLibs->setDescription('Add one or more additional highcharts libraries. see the list at <a href="https://code.highcharts.com/">code.highcharts.com</a>. Add only the filename and "modules/" if the file you want to add is in the modules folder. eg: modules/solid-gauge.js');

            $HighchartColours = MultiValueTextField::create('HighchartColours', 'Series colours')
                ->setAttribute('placeholder','#ff0000');
            $HighchartColours->setDescription('Add one or more hex formatted colours to this list to use in your charts. for example "#ff0000"');
            $fields->addFieldToTab('Root.Highcharts', $HighchartAllPages);
            $fields->addFieldToTab('Root.Highcharts', $HighchartColours);
            $fields->addFieldToTab('Root.Highcharts', $HighchartLibs);
            $fields->addFieldToTab('Root.Highcharts', $HighchartMoreLibs);
        }

    }

}

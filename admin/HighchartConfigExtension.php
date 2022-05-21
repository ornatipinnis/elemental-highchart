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
            'HighchartVersionNumber' => 'Varchar(4)',
            'HighchartLibraryAllPages' => 'Boolean',
            'HighchartLibs' => 'Varchar(255)',
            'HighchartColours' => 'MultiValueField'
        ];

        public function updateCMSFields(FieldList $fields) {
            $HighchartVersion = DropdownField::create('HighchartVersionNumber', 'Highchart version')->setSource(self::HighchartVersions());
            $HighchartVersion->setDescription('What version of Highcharts do you wish to use. Note that this will automatically use the latest version of the major branch you choose; eg. if you select "9" it will use 9.x.x. See the <a href="https://www.highcharts.com/blog/changelog/" target="_blank">Highchart changelog</a> for more details.');

            $HighchartAllPages = CheckboxField::create('HighchartLibraryAllPages', 'Use Highcharts libraries on all pages?');
            $HighchartAllPages->setDescription('Do you want to include the libraries on all pages or just the ones that use the Highcharts block? This is useful if you use other Highcharts integrations on your site.');

            $HighchartLibs = CheckboxSetField::create('HighchartLibs', 'Which libraries to include', [
                'chart' => 'Highcharts', 
                'stock' => 'Highcharts Stock', 
                //'maps' => 'Highcharts Maps', 
                //'gantt' => 'Highcharts Gantt'
            ]);
            $HighchartLibs->setDescription('Which libraries do you want to include on all pages?');

            $HighchartColours = MultiValueTextField::create('HighchartColours', 'Series colours')
                    ->setAttribute('placeholder','#ff0000');
            $HighchartColours->setDescription('Add one or more hex formatted colours to this list to use in your charts. for example "#ff0000"');
            $fields->addFieldToTab('Root.Highcharts', $HighchartAllPages);
            $fields->addFieldToTab('Root.Highcharts', $HighchartVersion);
            $fields->addFieldToTab('Root.Highcharts', $HighchartColours);
            $fields->addFieldToTab('Root.Highcharts', $HighchartLibs);
        }

        public static function HighchartVersions() {
            return [
                '0' => 'Latest',
                '10.0' => '10.x.x',
                '9.0' => '9.x.x',
                '8.0' => '8.x.x',
                '7.0' => '7.x.x'
            ];
        }

    }

}
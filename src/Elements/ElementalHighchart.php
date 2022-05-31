<?php

namespace aetchell\Highcharts\Elemental {

    use aetchell\Highcharts\Elemental\ElementalHighchartSeries;
    use aetchell\Highcharts\Libraries\HighchartsLibraries;
    use DNADesign\Elemental\Models\BaseElement;
    use SilverStripe\AssetAdmin\Forms\UploadField;
    use SilverStripe\Assets\File;
    use SilverStripe\Forms\CheckboxField;
    use SilverStripe\Forms\CompositeField;
    use SilverStripe\Forms\DropdownField;
    use SilverStripe\Forms\FieldGroup;
    use SilverStripe\Forms\FieldList;
    use SilverStripe\Forms\GridField\GridField;
    use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
    use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
    use SilverStripe\Forms\GridField\GridFieldFilterHeader;
    use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
    use SilverStripe\Forms\LiteralField;
    use SilverStripe\Forms\NumericField;
    use SilverStripe\Forms\OptionsetField;
    use SilverStripe\Forms\TextField;
    use SilverStripe\SiteConfig\SiteConfig;
    use SilverStripe\View\Parsers\ShortcodeParser;
    use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
    use UncleCheese\DisplayLogic\Forms\Wrapper;
    use const BASE_PATH;
    use function _t;

    class ElementalHighchart extends BaseElement {

        private static $singular_name = 'Highchart';
        private static $plural_name = 'Highcharts';
        private static $icon = 'font-icon-chart-line';
        private static $table_name = 'ElementalHighchart';
        private static $db = [
            'Content' => 'HTMLText',
            'ChartCaption' => 'HTMLText',
            'CSSClass' => 'Varchar(40)',
            'LibType' => 'Enum(array("chart","stock"),"chart")',
            'DefaultSeries' => 'Enum(array("line","spline","area","areaspline","bar","column","pie"),"line")',
            'DefaultSeriesLabel' => 'Varchar(20)',
            'DefaultSeriesTitle' => 'Varchar(40)',
            'DefaultXAxisTitle' => 'Varchar(40)',
            'ValuePrefix' => 'Varchar(20)',
            'ValueSuffix' => 'Varchar(20)',
            'ChartTitle' => 'Varchar(255)',
            'ChartSubtitle' => 'HTMLText',
            'DataSource' => 'Enum(array("CSV","API"),"CSV")',
            'RemoteDataSource' => 'Text',
            'EnablePolling' => 'Int',
            'EnableSeriesStacking' => 'Boolean',
            'SeriesStacking' => 'Enum(array("normal","percent"),"normal")',
            'EnableExporting' => 'Boolean',
            //highstock options
            'Navigator' => 'Boolean',
            'RangeSelector' => 'Boolean',
            'AllowFullscreen' => 'Boolean',
            'ConnectNulls' => 'Boolean',
            'ZoomType' => 'Boolean',
            'ChartHeight' => 'Int',
            'PieInnerSize' => 'Int',
            'Marker' => 'Boolean',
            'MarkerSymbol' => 'Enum(array("circle","square","triangle","triangle-down"),"circle")',
                // consider adding chart min/max
                // could get difficult to manage on multi-series views
                //'YMin' => 'Int',
                //'YMax' => 'Int',
        ];
        private static $has_one = [
            'File' => File::class
        ];
        private static $owns = [
            'File',
            'Series'
        ];
        private static $has_many = [
            'Series' => ElementalHighchartSeries::class,
        ];
        private static $defaults = [
            'LibType' => 'chart',
            'DefaultSeries' => 'line',
            'EnablePolling' => 0,
            'SeriesStacking' => 'normal',
            'Navigator' => false,
            'RangeSelector' => false,
            'EnableExporting' => true,
            'AllowFullscreen' => true,
            'ConnectNulls' => false,
            'ChartHeight' => '400',
            'PieInnerSize' => 0,
            'ZoomType' => true,
            'Marker' => true,
            'MarkerSymbol' => 'circle'
        ];
        private static $inline_editable = false;
        var $LibrariesExtra = [];

        /**
         *
         * @param type $record
         * @param type $isSingleton
         * @param type $model
         *
         * Add the remote Javascript here, based on chart type
         */
        function __construct($record = null, $isSingleton = false, $model = null) {
            parent::__construct($record, $isSingleton, $model);

            if (isset($this->ID) && $this->LibType > '0') {
                $this->LibrariesExtra['LibType'] = $this->LibType;

                if ($this->EnableExporting == true || $this->AllowFullscreen) {
                    $this->LibrariesExtra['Exporting'] = true;
                }
                $SiteConfig = SiteConfig::current_site_config();
                $charts = new HighchartsLibraries();
                $charts->Libraries($SiteConfig, $this->LibrariesExtra);
            }
        }

        /**
         * Enforce ChartHeight
         */
        public function onBeforeWrite() {
            parent::onBeforeWrite();

            if ((int) $this->ChartHeight <= 0) {
                $this->ChartHeight = (int) self::$defaults['ChartHeight'];
            }
            if ((int) $this->PieInnerSize <= 0) {
                $this->PieInnerSize = (int) self::$defaults['PieInnerSize'];
            }
        }

        /**
         * Builds the chart config
         *
         * Takes the object and compiles a HighCharts.options object which is passed to the constructor.
         *
         * @return string JSON encoded string
         */
        public function chartConfig() {
            $chart = (object) [
                        'chart' => ['type' => $this->DefaultSeries, 'animation' => true],
                        'credits' => ['enabled' => false],
                        'title' => ['text' => $this->ChartTitle],
                        'tooltip' => [
                            'shared' => true,
                            'valuePrefix' => ($this->ValuePrefix ? $this->ValuePrefix : ''),
                            'valueSuffix' => ($this->ValueSuffix ? $this->ValueSuffix : '')
                        ],
                        'data' => ['enablePolling' => false, 'dataRefreshRate' => 60],
                        'series' => [],
                        'plotOptions' => [
                            'series' => [
                                'allowPointSelect' => true,
                                'states' => [
                                    'select' => [
                                        'enabled' => true
                                    ]
                                ],
                                'marker' => [
                                    'enabled' => false
                                ],
                                'connectNulls' => $this->ConnectNulls == true && in_array($this->DefaultSeries, ['line', 'spline', 'area', 'areaspline', 'pie']) ? true : false
                            ]
                        ],
                        'exporting' => [
                            'enabled' => ($this->enableExporting == true ? true : false)
                        ],
                        'xAxis' => [
                            'title' => [
                                'Text' => false
                            ]
                        ],
            ];
            
            if($this->DefaultXAxisTitle !== '') {
                $chart['xAxis']['title']['text'] = $this->DefaultXAxisTitle;
            }

            if ($this->Marker == true && in_array($this->DefaultSeries, ['line', 'spline', 'area', 'areaspline'])) {
                $chart->plotOptions['series']['marker']['enabled'] = true;
                $chart->plotOptions['series']['marker']['symbol'] = $this->MarkerSymbol;
            }
            $SiteConfig = SiteConfig::current_site_config();

            if ($SiteConfig->HighchartColours !== '') {
                $chart->colors = explode(',', $SiteConfig->HighchartColours);
            }

            if ($this->LibType == 'stock') {
                $chart->rangeSelector['enabled'] = false;
                $chart->navigator['enabled'] = false;
                if ($this->RangeSelector == true) {
                    $chart->rangeSelector['enabled'] = true;
                }
                if ($this->Navigator == true) {
                    $chart->navigator['enabled'] = true;
                }
            }

            if ($this->EnableExporting == true) {
                $chart->exporting['enabled'] = true;
                $chart->exporting['filename'] = preg_replace('/\s+/', '-', ($this->ChartTitle ? $this->ChartTitle : 'chart-export')) . '-' . date('dmYHi');
            }

            if ($this->ZoomType == true) {
                $chart->chart['zoomType'] = 'xy';
            }

            if (
                    $this->allowDataSourcePolling() == true && $this->DataSource == 'API' && $this->DefaultSeries !== 'pie'
            ) {
                $chart->data['enablePolling'] = true;
                $chart->data['dataRefreshRate'] = (int) $this->EnablePolling;
            }

            if ($this->ChartSubtitle) {
                $chart->subtitle['text'] = (string) ShortcodeParser::get_active()->parse($this->ChartSubtitle);
                $chart->subtitle['useHTML'] = true;
            }

            if ($this->EnableSeriesStacking == true && $this->DefaultSeries !== 'pie') {
                $chart->plotOptions['series']['stacking'] = $this->SeriesStacking;
            }

            /**
             * No series config for pie charts
             */
            if (
                    $this->Series()->count() > 0 && $this->DefaultSeries !== 'pie'
            ) {
                $c = 0;
                foreach ($this->Series() as $s) {
                    if ($c <= 0) {
                        $s->ShowYAxis = true;
                    }
                    $chart->series[$c] = [
                        'type' => $s->SeriesType,
                        'id' => 'id' . $c,
                        'marker' => [
                            'enabled' => false
                        ],
                        'visible' => ($s->Visible == true ? true : false)
                    ];

                    $chart->yAxis[$c] = [
                        'opposite' => ($s->ShowYAxisPosition == 'right' ? true : false),
                        'title' => [
                            'text' => false
                        ]
                    ];

                    if ($s->ShowYAxis == true) {
                        $chart->yAxis[$c]['title']['text'] = ($s->SeriesTitle() ? $s->SeriesTitle() : false);
                        $chart->yAxis[$c]['labels']['format'] = '{value}' . ($s->SeriesLabel() ? $s->SeriesLabel() : '');
                        $chart->series[$c]['yAxis'] = $c;
                    }

                    if ($s->Marker == true && in_array($s->SeriesType, ['line', 'spline', 'area', 'areaspline'])) {
                        $chart->series[$c]['marker']['enabled'] = true;
                        $chart->series[$c]['marker']['symbol'] = $s->MarkerSymbol;
                    }

                    if ($s->ValuePrefix) {
                        $chart->series[$c]['tooltip']['valuePrefix'] = $s->ValuePrefix;
                    }

                    if ($s->ValueSuffix) {
                        $chart->series[$c]['tooltip']['valueSuffix'] = $s->ValueSuffix;
                    }

                    $c++;
                }
            } else {
                /**
                 * otherwise do a simple yAxis setup
                 */
                $chart->yAxis['title']['text'] = $this->DefaultSeriesTitle;
                $chart->yAxis['labels']['format'] = '{value}' . $this->DefaultSeriesLabel;
            }

            /**
             * Pie charts won't pull in the CSV data's category name so we have to manually fetch the data here for pie charts
             * @todo - use JS to parse the CSV and extract the names and dynamically add them to the series.
             * sigh
             */
            if ($this->DefaultSeries == 'pie') {
                $chart->series = $this->SeriesArray();
                $chart->data['enablePolling'] = false;
                $chart->plotOptions['pie']['innerSize'] = $this->PieInnerSize >= 0 ? $this->PieInnerSize : 0;
            } else {
                $chart->data['csvURL'] = $this->SeriesData();
            }

            return json_encode($chart);
        }

        /**
         * Example method to detect a certain series type. Can be helpful to know if certain libraries need to be included
         */
        private function hasSeriesType($type = 'line') {
            if ($this->Series()) {
                foreach ($this->Series() as $series) {
                    if ($series->SeriesType == (string) $type) {
                        return true;
                    }
                }
            }
            return false;
        }

        public function getCMSFields() {

            $this->beforeUpdateCMSFields(function (FieldList $fields) {

                $fields->removeByName('Content');
                $fields->removeByName('ChartCaption');
                $fields->removeByName('LibType');
                $fields->removeByName('DefaultSeries');
                $fields->removeByName('DefaultSeriesLabel');
                $fields->removeByName('DefaultSeriesTitle');
                $fields->removeByNAme('DefaultXAxisTitle');
                $fields->removeByName('ChartTitle');
                $fields->removeByName('ChartSubtitle');
                $fields->removeByName('CSSClass');
                $fields->removeByName('Series');
                $fields->removeByName('RemoteDataSource');
                $fields->removeByName('EnablePolling');
                $fields->removeByName('File');
                $fields->removeByName('DataSource');
                $fields->removeByName('EnableSeriesStacking');
                $fields->removeByName('SeriesStacking');
                $fields->removeByName('Navigator');
                $fields->removeByName('RangeSelector');
                $fields->removeByName('EnableExporting');
                $fields->removeByName('AllowFullscreen');
                $fields->removeByName('ConnectNulls');
                $fields->removeByName('ZoomType');
                $fields->removeByName('History'); // this one seems to have issues
                $fields->removeByName('ValuePrefix');
                $fields->removeByName('ValueSuffix');
                $fields->removeByName('ChartHeight');
                $fields->removeByName('PieInnerSize');
                $fields->removeByName('Marker');
                $fields->removeByName('MarkerSymbol');

                $Content = HTMLEditorField::create('Content', 'Content')->setRows(10)->setDescription('Appears in a column to the left of the chart.');

                $ChartCaption = HTMLEditorField::create('ChartCaption', 'Chart caption')->setRows(2)->setDescription('Appears below the chart.');

                $LibType = DropdownField::create('LibType', 'Library type')->setSource($this->dbObject('LibType')->enumValues());
                $LibType->setDescription('Select the base Highchart library here.');

                $DefaultSeries = DropdownField::create('DefaultSeries', 'Default series type')->setSource($this->dbObject('DefaultSeries')->enumValues());
                $DefaultSeries->setDescription('This is the default series rendering style. Highcharts uses the "line" style if no option is selected.');

                $CSSClass = TextField::create('CSSClass', 'CSS Class');
                $CSSClass->setDescription('Add a custom CSS class to this chart, you can add multiple classes here in the format "class1 class2 class3"');

                $ChartDesc = LiteralField::create('ChartDesc', '<p>For most charts the type should be "chart". You only need to select "stock" if your data is time base, require the navigator, range selector or want to add trendlines. Stock charts require the data to be categorised by time or date values.</p>');
                $ChartStyleDesc = LiteralField::create('ChartStyleDesc', '<p>Add a custom CSS class name to surround your chart. You shouldn\'t need to add anything here generally.</p><p>You can also set the height of the chart here, the default is ' . self::$defaults['ChartHeight'] . ' pixels high which is ideal for most charts.</p>');
                $ChartTitle = TextField::create('ChartTitle', 'Chart title');

                $DefaultSeriesTitle = TextField::create('DefaultSeriesTitle', 'Y axis title')
                        ->setAttribute('placeholder', 'Rainfall')
                        ->setDescription('The default Y axis title, this appears next to the Y axis, for example "Rainfall".');

                $DefaultXAxisTitle = TextField::create('DefaultXAxisTitle', 'X axis title')
                        ->setAttribute('placeholder', 'Years')
                        ->setDescription('The default X axis title, this appears above the chart legend on the X axis.');
                
                
                $DefaultSeriesLabel = TextField::create('DefaultSeriesLabel', 'Y axis label')
                        ->setAttribute('placeholder', 'mm')
                        ->setDescription('The default Y axis label, this appears next to the Y axis value(s) that is being measured, for example "mm" if the chart displays rainfall data. If you set Y axis labels on a custom series config then it will override this chart wide value.');
                $ValuePrefix = TextField::create('ValuePrefix', 'Value prefix');
                $ValueSuffix = TextField::create('ValueSuffix', 'Value suffix');

                $SeriesConfigHelp = LiteralField::create('SeriesConfigHelp', '<p>The default Y axis label, this appears next to the Y axis value(s) that is being measured, for example "mm" if the chart displays rainfall data. If you set Y axis labels on a custom series config then it will override this chart wide value.</p>');

                $ChartSubtitle = HTMLEditorField::create('ChartSubtitle', 'Chart Subtitle')->setRows(3);

                $GridConf = GridFieldConfig_RelationEditor::create(10);
                $GridConf->addComponent(new GridFieldOrderableRows('SeriesOrder'));

                $remove = [];
                $remove[] = GridFieldAddExistingAutocompleter::class;
                $remove[] = GridFieldFilterHeader::class;
                if (count($remove) > 0) {
                    $GridConf->removeComponentsByType($remove);
                }

                $Series = new GridField('Series', 'Series', $this->Series(), $GridConf);
                $Series->setDescription('Add one or more series configs and order them manually.');

                $RemoteDataSource = TextField::create('RemoteDataSource', 'Remote data source');
                $RemoteDataSource->setDescription('If your data is pulled from an API or remove source, enter the full URL to the data here.');

                $EnablePolling = NumericField::create('EnablePolling', 'Poll for new data interval');
                $EnablePolling->setDescription('Poll for new data every x seconds. Polling will only occur if the value is above 0. Polling is only possible when the datasouce is set to "API" and the chart type is not "pie"');

                $ChartHeight = NumericField::create('ChartHeight', 'Chart height');
                $ChartHeight->setDescription('Height of the chart on the page in pixels.');
                $ChartHeight->setAttribute('placeholder', self::$defaults['ChartHeight']);

                $File = UploadField::create('File', 'CSV data source');
                $File->setDescription('Upload a CSV file containing the data source.');
                $File->setFolderName('ElementalHighchartDataSet');
                $File->getValidator()->setAllowedExtensions(['csv']);

                $DataSource = OptionsetField::create('DataSource', 'Data source')->setSource($this->dbObject('DataSource')->enumValues());

                $EnableSeriesStacking = CheckboxField::create('EnableSeriesStacking', 'Stack series on this chart');
                $EnableSeriesStacking->setDescription('This will enable series stacking, note that this will not work or make sense for certain series types.');

                $SeriesStacking = DropdownField::create('SeriesStacking', 'Stacking type')->setSource($this->dbObject('SeriesStacking')->enumValues());

                $EnableExporting = CheckboxField::create('EnableExporting', 'Allow exporting of chart data');
                $EnableExporting->setDescription('This will enable the exporting menu on the chart.');

                $AllowFullscreen = CheckboxField::create('AllowFullscreen', 'Add fullscreen button');
                $AllowFullscreen->setDescription('This adds an option to open the chart in full screen mode from a hyperlink.');

                $ZoomType = CheckboxField::create('ZoomType', 'Allow zoom');
                $ZoomType->setDescription('Allow zoom by dragging the mouse.');

                $PieInnerSize = NumericField::create('PieInnerSize', 'Pie chart inner size (px)');
                $PieInnerSize->setDescription('If this value is greater than zero then the pie chart will be rendered as a donut chart. The value is the size of the hole in pixels');

                $HighchartsLink = LiteralField::create('HighchartsLink', file_get_contents(dirname(dirname(dirname(__FILE__))) . '/docs/highchartsHelp.html'));

                $MarkerNotes = LiteralField::create('MarkerNotes', '<div>These options are only availble for "line","spline", "area" and "areaspline" series.</div>');
                $Marker = CheckboxField::create('Marker', 'Show data point markers');
                $Marker->setDescription('Show markers on each data point. Markers are only available for certain series types such as "line" and "spline"');

                $MarkerSymbol = DropdownField::create('MarkerSymbol', 'Marker symbol')->setSource($this->dbObject('MarkerSymbol')->enumValues());
                $MarkerSymbol->setDescription('Which symbol to use for the data point marker.');

                /**
                 * Highstock fields
                 */
                $Navigator = CheckboxField::create('Navigator', 'Show chart navigator');
                $Navigator->setDescription('The navigator is a small series below the main series, displaying a view of the entire data set. It provides tools to zoom in and out on parts of the data as well as panning across the dataset.');

                $RangeSelector = CheckboxField::create('RangeSelector', 'Show chart range selector');
                $RangeSelector->setDescription('The range selector is a tool for selecting ranges to display within the chart. It provides buttons to select pre-configured ranges in the chart, like 1 day, 1 week, 1 month, etc. It also provides input boxes where min and max dates can be manually input.');

                $ConnectNulls = CheckboxField::create('ConnectNulls', 'Connect null points');
                $ConnectNulls->setDescription('Whether to connect a graph line across null points, or render a gap between the two points on either side of the null. This only works on "line", "spline", "area", "areaspline" and "pie" series');
                $ConnectNulls->displayIf('DefaultSeries')->isEqualTo('line')
                        ->orIf('DefaultSeries')->isEqualTo('spline')
                        ->orIf('DefaultSeries')->isEqualTo('area')
                        ->orIf('DefaultSeries')->isEqualTo('areaspline')
                        ->orIf('DefaultSeries')->isEqualTo('pie');

                /**
                 * Display logic
                 */
                $File->displayIf('DataSource')->isEqualTo('CSV');
                $RemoteDataSource->displayIf('DataSource')->isEqualTo('API');
                $EnablePolling->displayIf('DataSource')->isEqualTo('API')->andIf('DefaultSeries')->isNotEqualTo('pie');
                $EnableSeriesStacking->displayIf('DefaultSeries')->isNotEqualTo('pie');
                $SeriesStacking->displayIf('EnableSeriesStacking')->isChecked();

                $Navigator->displayIf('LibType')->isEqualTo('stock');
                $RangeSelector->displayIf('LibType')->isEqualTo('stock');

                $PieInnerSize->displayIf('DefaultSeries')->isEqualTo('pie');
                $MarkerNotes->displayIf('DefaultSeries')->isEqualTo('line')
                        ->orIf('DefaultSeries')->isEqualTo('spline')
                        ->orIf('DefaultSeries')->isEqualTo('areaspline')
                        ->orIf('DefaultSeries')->isEqualTo('area');

                $MarkerSymbol->displayIf('Marker')->isChecked();
                /**
                 * Add fields to page
                 */
                $fields->addFieldToTab('Root.Main', $Content);
                $fields->addFieldToTab('Root.Main', $ChartCaption);

                $fields->addFieldToTab('Root.ChartConfig',
                        CompositeField::create(FieldGroup::create(
                                        $LibType,
                                        $DefaultSeries),
                                $ChartDesc,
                                FieldGroup::create(
                                        $CSSClass,
                                        $ChartHeight
                                ),
                                $ChartStyleDesc,
                                $ChartTitle,
                                $ChartSubtitle
                        )->setColumnCount(2)->setTitle('Chart config')
                );

                $fields->addFieldToTab('Root.ChartConfig', CompositeField::create(
                                $EnableSeriesStacking,
                                $SeriesStacking,
                                $EnableExporting,
                                $AllowFullscreen,
                                $ConnectNulls,
                                $Navigator,
                                $RangeSelector,
                                $ZoomType,
                                $PieInnerSize,
                                $MarkerNotes,
                                $Marker,
                                $MarkerSymbol
                        )->setTitle('Chart attributes'));

                $fields->addFieldToTab('Root.ChartData', $DataSource);
                $fields->addFieldToTab('Root.ChartData', $RemoteDataSource);
                $fields->addFieldToTab('Root.ChartData', $EnablePolling);
                $fields->addFieldToTab('Root.ChartData', $File);
                $fields->addFieldToTab('Root.ChartData', CompositeField::create(FieldGroup::create(
                                        $DefaultXAxisTitle,
                                        $DefaultSeriesTitle,
                                        $DefaultSeriesLabel,
                                        $ValuePrefix,
                                        $ValueSuffix
                                ),
                                $SeriesConfigHelp
                        )->setTitle('Default series formatting'));
                $fields->addFieldToTab('Root.ChartData', Wrapper::create($Series)->hideIf('DefaultSeries')->isEqualTo('pie')->end());
                $fields->addFieldToTab('Root.Help', $HighchartsLink);
            });
            return parent::getCMSFields();
        }

        /**
         * What type of chart are we rendering?
         *
         * this is used to set the correct object to use when rendering a chart; 'chart' or stockChart
         * for example:
         * Highcharts.chart({highchartsOptions});
         *
         * @return string
         */
        public function getLibTypeClass() {
            switch ($this->LibType) {
                case 'stock';
                    $r = 'stockChart';
                    break;
                default:
                    $r = 'chart';
                    break;
            }
            return $r;
        }

        /**
         * This returns a path to a CSV formatted response.
         *
         * @return string A path to a an API endpoint or a file
         */
        public function SeriesData() {
            $data = false;

            /**
             * Get data from filesource
             */
            if (
                    $this->DataSource == 'CSV' && $this->File() && file_exists(BASE_PATH . '/public' . $this->File()->Url)
            ) {
                $data = $this->File()->Url;

                /**
                 * get data from API endpoint
                 */
            } else if (
                    $this->RemoteDataSource && $this->DataSource == 'API'
            ) {
                $data = $this->RemoteDataSource;
            }

            return $data;
        }

        public function SeriesArray() {
            if ($this->SeriesData()) {
                $row = 0;
                $dataArray = array();
                $dataFilePath = (
                        $this->DataSource == 'CSV' ? BASE_PATH . '/public' . $this->SeriesData() : $this->SeriesData()
                        );
                if (($handle = fopen($dataFilePath, "r")) !== FALSE) {
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $num = count($data);
                        if ($row > 0) {
                            for ($c = 0; $c < $num; $c++) {
                                if ($c > 0) {
                                    if (is_numeric(trim($data[$c]))) {
                                        $valueRow = ($c + ( ($num - 1) / 2 ));
                                        $dataArray[$headers[$c]]['name'] = $headers[$c];
                                        $dataArray[$headers[$c]]['data'][] = array(
                                            'y' => (float) $data[$c],
                                            'name' => $data[0]
                                        );
                                    }
                                }
                            }
                        } else {
                            $headers = $data;
                        }
                        $row++;
                    }
                    fclose($handle);
                }
                if (count($dataArray) > 0) {
                    foreach ($dataArray as $k => $v) {
                        $JSONArray[] = $v;
                    }

                    return $JSONArray;
                } else {
                    return false;
                }
            }
        }

        public function allowDataSourcePolling() {
            if ((int) $this->EnablePolling > 0) {
                return true;
            }
            return false;
        }

        /**
         * Internal Elemental block methods
         */

        /**
         *
         * @return type
         */
        public function getType() {
            return _t(__CLASS__ . '.BlockType', 'Highchart');
        }

        /**
         *
         * @return string
         */
        public function getSummary() {
            return '';
        }

        /**
         * Return file title and thumbnail for summary section of ElementEditor
         *
         * @return array
         */
        protected function provideBlockSchema() {
            $blockSchema = parent::provideBlockSchema();
            $blockSchema['content'] = $this->Title . ' - ' . $this->LibType . '/' . $this->DefaultSeries . ' - datasource: ' . $this->DataSource;
            return $blockSchema;
        }

    }

}
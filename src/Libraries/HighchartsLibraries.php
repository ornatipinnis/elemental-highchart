<?php

namespace aetchell\Highcharts\Libraries {

    use SilverStripe\View\Requirements;
    use SilverStripe\Core\Manifest\ModuleResourceLoader;

    class HighchartsLibraries {

        /**
         * Create an array to store the list of Highcharts files to include
         *
         * @var array
         */
        public $HighchartsJSRequire = [];

        /**
         * Get the base URL for Highcharts resources using ModuleResourceLoader
         * Do not use the code from the CDN anymore as it has rate limitations
         * https://code.highcharts.com;
         *
         * @return string
         */
        private static function getHighchartsURLBase()
        {
            return ModuleResourceLoader::singleton()->resolveURL('aetchell/elemental-highchart:client/js/code/');
        }

        public function Libraries($SiteConfig = false, $Extra = false) {
            $HighchartsJSRequire = [];

            /**
             * Internal JS file for controlling charts via javascript.
             * Not currently in use.
             */
            // $HighchartsJSRequire[] = ['aetchell/elemental-highchart:client/js/HighchartElemental.js'];

            $HighchartAdditionalLibs = false;
            $HighchartsURLBase = self::getHighchartsURLBase();

            if ($SiteConfig->HighchartAdditionalLibs != '') {
                $HighchartAdditionalLibs = explode(',',$SiteConfig->HighchartAdditionalLibs);
            }

            /**
             * Create a map of files to include here, maybe add all these into the config page rather than hardcode into classes
             */
            $chartsGlobal = (json_decode($SiteConfig->HighchartLibs && $SiteConfig->HighchartLibraryAllPages == true) ? json_decode($SiteConfig->HighchartLibs) : []);
            $charts = $chartsGlobal;
            if (isset($Extra['LibType'])) {
                $charts = array_merge($chartsGlobal, [$Extra['LibType']]);
            }
            foreach ($charts as $Type) {
                switch ($Type) {

                    case 'stock':
                    case 'chart':

                        $HighchartsJSRequire[] = [$HighchartsURLBase,  'highstock.js'];
                        $HighchartsJSRequire[] = [$HighchartsURLBase, 'highcharts-more.js'];
                        $HighchartsJSRequire[] =  [$HighchartsURLBase, 'modules/exporting.js'];
                        $HighchartsJSRequire[] = [$HighchartsURLBase, 'modules/data.js'];

                        if (isset($Extra['Exporting']) && $Extra['Exporting'] == true) {
                            $HighchartsJSRequire[] = [$HighchartsURLBase, 'modules/exporting.js'];
                            $HighchartsJSRequire[] = [$HighchartsURLBase, 'modules/offline-exporting.js'];
                            $HighchartsJSRequire[] = [$HighchartsURLBase, 'modules/export-data.js'];
                        }

                        if(
                            is_array($HighchartAdditionalLibs)
                            && count($HighchartAdditionalLibs) >= 1
                        ) {
                            foreach($HighchartAdditionalLibs as $extraFile) {
                                $HighchartsJSRequire[] = [$HighchartsURLBase, $extraFile];
                            }
                        }
                        break;
                    /**
                     * Not used yet
                     */
//                    case 'maps':
//
//                        break;

                    /**
                     * Not used yet
                     */
//                    case 'gantt':
//
//                        break;

                    default:
                        break;
                }
            }
            foreach ($HighchartsJSRequire as $HcJS) {
                Requirements::javascript(implode('/', $HcJS), ['defer' => true]);
            }
            //Requirements::css('aetchell/elemental-highchart:client/css/HighchartElemental.css');
        }

    }

}

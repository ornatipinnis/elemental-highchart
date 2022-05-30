<?php

namespace aetchell\Highcharts\Libraries {

    use SilverStripe\View\Requirements;

    class HighchartsLibraries {

        public $HighchartsJSRequire = [];
        private static $HighchartsURLBase = 'https://code.highcharts.com';

        public function Libraries($SiteConfig = false, $Extra = false) {
            $HighchartsJSRequire = [];
            $HighchartsJSRequire[] = ['aetchell/elemental-highchart:client/js/HighchartElemental.js'];

            $HighchartToUse = false;

            if (
                    isset($SiteConfig->HighchartVersionNumber) && $SiteConfig->HighchartVersionNumber !== '' && $SiteConfig->HighchartVersionNumber > 0
            ) {
                $HighchartToUse = $SiteConfig->HighchartVersionNumber;
            }
            
            if (
                    isset($SiteConfig->HighchartAdditionalLibs) && $SiteConfig->HighchartAdditionalLibs !== '' 
            ) {
                $HighchartAdditionalLibs = json_decode($SiteConfig->HighchartAdditionalLibs);
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
                        $HighchartsJSRequire[] = ($HighchartToUse !== false ?
                                [self::$HighchartsURLBase, 'stock', $HighchartToUse, 'highstock.js'] :
                                [self::$HighchartsURLBase, 'stock', 'highstock.js']);

                        $HighchartsJSRequire[] = ($HighchartToUse !== false ?
                                [self::$HighchartsURLBase, 'stock', $HighchartToUse, 'highcharts-more.js'] :
                                [self::$HighchartsURLBase, 'stock', 'highcharts-more.js']);

                        $HighchartsJSRequire[] = ($HighchartToUse !== false ?
                                [self::$HighchartsURLBase, $HighchartToUse, 'modules/exporting.js'] :
                                [self::$HighchartsURLBase, 'modules/exporting.js']);

                        $HighchartsJSRequire[] = ($HighchartToUse !== false ?
                                [self::$HighchartsURLBase, $HighchartToUse, 'modules/data.js'] :
                                [self::$HighchartsURLBase, 'modules/data.js']);
                        
                        $HighchartsJSRequire[] = ($HighchartToUse !== false ?
                                [self::$HighchartsURLBase, $HighchartToUse, 'modules/solid-gauge.js'] :
                                [self::$HighchartsURLBase, 'modules/solid-gauge.js']);
                        
                        if(is_array($HighchartAdditionalLibs)) {
                            foreach($HighchartAdditionalLibs as $extraFile) {
                                $HighchartsJSRequire[] = ($HighchartToUse !== false ?
                                        [self::$HighchartsURLBase, $HighchartToUse, $extraFile] :
                                        [self::$HighchartsURLBase, $extraFile]);                                
                            }
                        }
                        
                        if (isset($Extra['Exporting']) && $Extra['Exporting'] == true) {
                            $HighchartsJSRequire[] = ($HighchartToUse !== false ?
                                [self::$HighchartsURLBase, $HighchartToUse, 'modules/exporting.js'] :
                                [self::$HighchartsURLBase, 'modules/exporting.js']);                          
                            
                            $HighchartsJSRequire[] = ($HighchartToUse !== false ?
                                [self::$HighchartsURLBase, $HighchartToUse, 'modules/offline-exporting.js'] :
                                [self::$HighchartsURLBase, 'modules/offline-exporting.js']);  

                            $HighchartsJSRequire[] = ($HighchartToUse !== false ?
                                [self::$HighchartsURLBase, $HighchartToUse, 'modules/export-data.js'] :
                                [self::$HighchartsURLBase, 'modules/export-data.js']);                              
                        }



                        break;
                    /**
                     * Not used yet
                     */
                    case 'maps':

                        break;

                    /**
                     * Not used yet
                     */
                    case 'gantt':

                        break;

                    default:
                        break;
                }
            }
            foreach ($HighchartsJSRequire as $HcJS) {
                Requirements::javascript(implode('/', $HcJS), ['defer' => true]);
            }
            Requirements::css('aetchell/elemental-highchart:client/css/HighchartElemental.css');
        }

    }

}
<?php

namespace aetchell\Highcharts\Extensions {

use aetchell\Highcharts\Libraries\HighchartsLibraries;
use SilverStripe\Core\Extension;
use SilverStripe\SiteConfig\SiteConfig;

    class HighchartsLibraryControllerExtension extends Extension {

        public function onAfterInit() {
            $SiteConfig = SiteConfig::current_site_config();
            if ($SiteConfig->HighchartLibraryAllPages == true) {
                $charts = new HighchartsLibraries();
                $charts->Libraries($SiteConfig);
            }
        }

    }

}

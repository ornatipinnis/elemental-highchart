<?php

namespace aetchell\Highcharts\Extensions {

    use aetchell\Highcharts\HighchartsLibraries;
    use SilverStripe\ORM\DataExtension;
    use SilverStripe\SiteConfig\SiteConfig;

    class HighchartsLibraryControllerExtension extends DataExtension {

        public function onAfterInit() {
            $SiteConfig = SiteConfig::current_site_config();
            if ($SiteConfig->HighchartLibraryAllPages == true) {
                $charts = new HighchartsLibraries();
                $charts->Libraries($SiteConfig);
            }
        }

    }

}
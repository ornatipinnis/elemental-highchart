<script type="application/javascript">                
    document.addEventListener(
        "DOMContentLoaded",
        () => {                 
            Highcharts.setOptions({
                lang: {
                  thousandsSep: ','
                }                
            });
            chartInst{$ID} = Highcharts.{$getLibTypeClass}(
                'elemental-highchart{$ID}',
                $chartConfig.RAW
            );

            <% if $EnableExporting %>
            let ehcsva{$ID} = document.getElementById("eh-ehcsva-activator{$ID}");
            ehcsva{$ID}.addEventListener('click', () => { 
                chartInst{$ID}.downloadCSV();
            });
            <% end_if %>

            <% if $AllowFullscreen %>
            /** content policy headers may prevent this from working **/
            let ehfsa{$ID} = document.getElementById("eh-fs-activator{$ID}");

            ehfsa{$ID}.addEventListener('click', function() {
                chartInst{$ID}.fullscreen.open();
                chartInst{$ID}.update({
                    exporting: {
                        menuItemDefinitions: {
                            viewFullscreen: {
                                textKey: 'exitFullscreen'
                            }
                        }
                    }
                });
            });
            <% end_if %>
            window.dispatchEvent(new Event('resize'));
        }
    );
</script>
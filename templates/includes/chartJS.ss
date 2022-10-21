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
                            chartInst{$ID}.update({
                                exporting: {
                                    menuItemDefinitions: {
                                        viewFullscreen: {
                                            textKey: 'viewFullscreen',
                                            onclick: function () {
                                                this.update({
                                                    tooltip: {
                                                        outside: this.fullscreen.isOpen
                                                    }
                                                });
                                                console.log('setting');
                                                this.fullscreen.toggle();
                                            }                                                
                                        }
                                    }
                                }
                            });
            ehfsa{$ID}.addEventListener('click', function() {
                chartInst{$ID}.fullscreen.open();
                chartInst{$ID}.update({
                    tooltip: {
                        outside: false
                    },
                    exporting: {
                        menuItemDefinitions: {
                            viewFullscreen: {
                                textKey: 'exitFullscreen',

                            }
                        }
                    }
                });                                
                chartInst{$ID}.fullscreen.toggle();
            });
            <% end_if %>
            window.dispatchEvent(new Event('resize'));
        }
    );
</script>

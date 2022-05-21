<div class="elemental-highchart content-element__content<% if $Style %> $StyleVariant<% end_if %>">
    <% if $ShowTitle %>
    <h2 class="content-element__title">$Title</h2>
    <% end_if %>
    <% if $SeriesData %>
    <div class="<% if $CSSClass %>{$CSSClass}<% end_if %>">
        <% if $Content %>
        <div class="">$Content</div>
        <% end_if %>
        <div class="">
            <div class="highchart-container">
                <div id="elemental-highchart{$ID}" class="highchart" data-type="{$LibType}" style="height: {$ChartHeight}px;"></div>
                <% if $ChartCaption || $AllowFullscreen %>
                <div class="chart-caption">
                    $ChartCaption                
                </div>
                <div class="chart-controls">
                <% if $EnableExporting %><a href="javascript:void(0);" id="eh-ehcsva-activator{$ID}"><i class="fa fa-table"></i> Source data</a><% end_if %>
                <% if $AllowFullscreen %><a href="javascript:void(0);" id="eh-fs-activator{$ID}"><i class="fa fa-expand"></i> View in full screen</a><% end_if %>
                </div>
                <% end_if %>
                <script type="application/javascript">
                    <% include chartJS %>
                    document.addEventListener(
                        "DOMContentLoaded",
                        () => {                 
                            
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
            </div>
        </div>
    </div>
    <% end_if %>
</div>
<div class="elemental-highchart content-element__content<% if $Style %> $StyleVariant<% end_if %>">
    <% if $ShowTitle %>
    <h2 class="content-element__title">$Title</h2>
    <% end_if %>
    <% if $SeriesData %>
    <div class="<% if $CSSClass %>{$CSSClass}<% end_if %> row">
        <% if $Content %>
        <div class="col-12 col-md-4 content">$Content</div>
        <% end_if %>
        <div class="col col-12 col-md">
            <div class="highchart-container">
                <div id="elemental-highchart{$ID}" class="highchart" data-type="{$LibType}" style="height: {$ChartHeight}px;"></div>
                <script type="application/javascript">
                    document.addEventListener(
                        "DOMContentLoaded",
                        () => {
                            chartInst{$ID} = Highcharts.{$getLibTypeClass}(
                                'elemental-highchart{$ID}',
                                $chartConfig.RAW
                            );
                    
                            <% if $DefaultSeries == 'pie' %>
                            chartInst{$ID}.update({
                                data : {
                                    parsed: function(columns) {
                // Keep the first item which is the series name, then remove the following 70
                console.log(columns[0]);                                      
                                    }
                                }
                            });
                            <% end_if %>
                    

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
                        }
                    );
                </script>

                <% if $ChartCaption || $AllowFullscreen %>
                <div class="chart-caption">
                    $ChartCaption                
                </div>
                    
                    <div   class="chart-controls">
                    <% if $EnableExporting %><a href="javascript:void(0);" id="eh-ehcsva-activator{$ID}"><i class="fa fa-table"></i> Source data</a><% end_if %>
                    <% if $AllowFullscreen %><a href="javascript:void(0);" id="eh-fs-activator{$ID}"><i class="fa fa-expand"></i> View in full screen</a><% end_if %>
                    </div>
                    
                <% end_if %>
            </div>

            <% if $AllowModal %>
            <div class="modal fade" id="highcharts-modal{$ID}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" style="display:none;">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div id="elemental-highchart-modal{$ID}" class="highchart" data-type="{$LibType}"></div>
                            <div class="chart-caption">$ChartCaption</div>
                        </div>
                    </div>
                </div>
            </div>
            <% end_if %>
        </div>
    </div>
    <% end_if %>
</div>
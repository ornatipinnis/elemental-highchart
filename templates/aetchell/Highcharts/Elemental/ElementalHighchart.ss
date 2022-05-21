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
                <% include chartJS %>
            </div>
        </div>
    </div>
    <% end_if %>
</div>
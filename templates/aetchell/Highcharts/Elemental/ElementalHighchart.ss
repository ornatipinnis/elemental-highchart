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
            <figure class="highchart-container">
                <div id="elemental-highchart{$ID}" class="highchart" data-type="{$LibType}" style="height: {$ChartHeight}px;"></div>
                <% if $ChartCaption || $AllowFullscreen %>
                <figcaption class="chart-caption">
                    $ChartCaption                
                </figcaption>
                <div class="chart-controls">
                <% if $EnableExporting %><i class="fa fa-download"></i> <a href="javascript:void(0);" id="eh-ehcsva-activator{$ID}">Download data</a><% end_if %>
                <% if $AllowFullscreen %><i class="fa fa-expand"></i> <a href="javascript:void(0);" id="eh-fs-activator{$ID}">View full screen</a><% end_if %>
                </div>
                <% end_if %>
                <% include chartJS %>
            </figure>
        </div>
    </div>
    <% end_if %>
</div>
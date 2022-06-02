<div class="container video">
    <% if VideoType == 'local' %>
        <video
            src="$video.URL"
            <% if $Cover %>
                poster="$Thumbnail.ScaleWidth(1800).URL"
            <% end_if %>
            <% if $Autoplay %>
                autoplay muted
            <% end_if %>
            <% if $ShowControls %>
                controls
            <% end_if %>
            <% if $Loop %>
                loop
            <% end_if %>
        />
    <% else_if VideoType == 'youtube' %>
        <div class="ratio ratio-16x9">
        <iframe src="https://www.youtube.com/embed/$VideoIdentifier?rel=0<% if $Autoplay %>&autoplay=1<% end_if %><% if not $ShowControls %>&controls=0<% end_if %><% if $Loop %>&loop=1&playlist=$VideoIdentifier<% end_if %>" title="YouTube video" allowfullscreen></iframe>
        </div>
    <% end_if %>
</div>

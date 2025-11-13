<p>
    You can edit TradeMe lists for ...
    <% loop $MainLinks %>
        <% if $IsCurrent %>
            <strong>$Title</strong>
        <% else %>
        <a href="$Link">$Title</a>
        <% end_if %>
        <% if $Last %><% else %> / <% end_if %>
    <% end_loop %>
</p>

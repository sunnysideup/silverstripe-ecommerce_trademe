<ul class="inline-links">
    <li>
        Filter by value:
    </li>

    <% loop FilterLinks %>
    <li><a href="$Link" class="$LinkingMode">$Title</a><% if $First %> // <% else %> / <% end_if %></li>
    <% end_loop %>
</ul>

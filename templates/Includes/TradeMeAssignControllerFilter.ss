<ul class="inline-links">
    <li>
        Filter by setting:
    </li>

    <% loop FilterLinks %>
    <li><a href="$Link" class="$LinkingMode">$Title</a><% if $Last %>.<% else %>, <% end_if %></li>
    <% end_loop %>
</ul>

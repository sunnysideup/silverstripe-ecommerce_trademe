<ul class="inline-links">
    <li>
        Filter by setting:
    </li>

    <% loop FilterLinks %>
    <li><a href="$Link" class="$LinkingMode">$Title</a></li>
    <% end_loop %>
</ul>

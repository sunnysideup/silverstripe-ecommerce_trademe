<div class="pagination">
<% if $ListForForm.MoreThanOnePage %>
    <% if $ListForForm.NotFirstPage %>
    <a class="prev" href="$ListForForm.PrevLink">←</a>
    <% end_if %>
    <% loop $ListForForm.PaginationSummary %>
        <% if $CurrentBool %>
            <span>$PageNum</span>
        <% else %>
            <% if $Link %>
                <a href="$Link">$PageNum</a>
            <% else %>
                ...
            <% end_if %>
        <% end_if %>
    <% end_loop %>
    <% if $ListForForm.NotLastPage %>
        <a class="next" href="$ListForForm.NextLink">→</a>
    <% end_if %>
<% end_if %>

</div>

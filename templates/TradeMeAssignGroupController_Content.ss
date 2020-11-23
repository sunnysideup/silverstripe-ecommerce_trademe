<% include TradeMeAssignControllerHeader %>
<body>
    <div class="form-holder-trade-me">
        <div class="intro">
            <h1>$Title</h1>
            <% include TradeMeAssignControllerFilter %>
            <% include TradeMeAssignControllerOtherLinks %>
            <h4>
                Below is a list of all product categories on the site with their associated TradeMe Category.
                <% if $Filter %>Filtered for <em>$Filter</em> ($FilterCount).<% end_if %>
            </h4>
        </div>
        <% include TradeMeAssignControllerPagination %>
        $Form
        <% include TradeMeAssignControllerPagination %>
        <% include TradeMeAssignControllerOtherLinks %>
    </div>
</body>
</html>

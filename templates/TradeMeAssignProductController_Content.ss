<% include Sunnysideup\EcommerceTrademe\IncludesTradeMeAssignControllerHeader %>
<body>
    <div class="form-holder-trade-me">
        <div class="intro">
            <h1>$Title</h1>
            <% include Sunnysideup\EcommerceTrademe\IncludesTradeMeAssignControllerFilter %>
            <% include Sunnysideup\EcommerceTrademe\IncludesTradeMeAssignControllerOtherLinks %>
            <h4>
                Below is a list of of all products
                <% if $ProductGroup.Title %>
                In the $ProductGroup.Title category.
                <% else %>
                on the site.
                <% end_if %>

                <% if $Filter %>Filtered for <em>$Filter</em> ($FilterCount products are in this list).<% end_if %>

                <% if $ProductGroup.ListProductsOnTradeMe %>
                    By default <u>$ProductGroup.ListProductsOnTradeMe</u> of the products
                    in the <a href="$ProductGroup.CMSEditLink">$ProductGroup.Title</a> category are sent to TradeMe.
                <% end_if %>
            </h4>

        </div>
        <% include Sunnysideup\EcommerceTrademe\IncludesTradeMeAssignControllerPagination %>
        $Form
        <% include Sunnysideup\EcommerceTrademe\IncludesTradeMeAssignControllerPagination %>
        <% include Sunnysideup\EcommerceTrademe\IncludesTradeMeAssignControllerOtherLinks %>
    </div>
</body>
</html>

<% include TradeMeAssignControllerHeader %>
<body>
    <div class="form-holder-trade-me">
        <div class="intro">
            <h1>$Title</h1>
            <ul class="inline-links">
                <li>
                    Filter by setting:
                </li>

                <% loop FilterLinks %>
                <li><a href="$Link" class="$LinkingMode">$Title</a></li>
                <% end_loop %>

            </ul>

            <h4>
                Below is a list of all products that are currently added to TradeMe.
                By default <strong>$ProductGroup.ListProductsOnTradeMe</strong> products
                in the <a href="$ProductGroup.CMSEditLink">$ProductGroup.Title</a> category are sent to TradeMe.
            </h4>

        </div>
        $Form
    </div>
</body>
</html>

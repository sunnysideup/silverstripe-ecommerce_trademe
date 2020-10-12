<% include TradeMeAssignControllerHeader %>
<body>
    <div class="form-holder-trade-me">
        <div class="intro">
            <h1>$Title</h1>
            <% include TradeMeAssignControllerFilter %>
            <h4>
                Below is a list of all products that are currently added to TradeMe.
                By default <u>$ProductGroup.ListProductsOnTradeMe</u> products
                in the <a href="$ProductGroup.CMSEditLink">$ProductGroup.Title</a> category are sent to TradeMe.
            </h4>

        </div>
        <% include TradeMeAssignControllerPagination %>
        $Form
        <% include TradeMeAssignControllerPagination %>
    </div>
</body>
</html>

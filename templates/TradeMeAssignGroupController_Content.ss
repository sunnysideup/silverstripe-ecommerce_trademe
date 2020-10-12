<% include TradeMeAssignControllerHeader %>
<body>
    <div class="form-holder-trade-me">
        <div class="intro">
            <h1>$Title</h1>
            <% include TradeMeAssignControllerFilter %>
            <h4>
                Below is a list of all product categories on the site with their associated TradeMe Category.
            </h4>
        </div>
        <% include TradeMeAssignControllerPagination %>
        $Form
        <% include TradeMeAssignControllerPagination %>
    </div>
</body>
</html>

<% include Sunnysideup\EcommerceTrademe\Includes\TradeMeAssignControllerHeader %>
<body>
    <div class="form-holder-trade-me">
        <div class="intro">
            <h1>$Title</h1>
            <% include Sunnysideup\EcommerceTrademe\Includes\TradeMeAssignControllerFilter %>
            <% include Sunnysideup\EcommerceTrademe\Includes\TradeMeAssignControllerOtherLinks %>
            <h4>
                Below is a list of all product categories on the site with their associated TradeMe Category.
                You can select the rules for each category in terms of what products are sent to TradeMe.
            </h4>
        </div>
        <% include Sunnysideup\EcommerceTrademe\Includes\TradeMeAssignControllerPagination %>
        $Form
        <% include Sunnysideup\EcommerceTrademe\Includes\TradeMeAssignControllerPagination %>
        <% include Sunnysideup\EcommerceTrademe\Includes\TradeMeAssignControllerOtherLinks %>
    </div>
</body>
</html>

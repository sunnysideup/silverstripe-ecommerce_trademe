<% include Sunnysideup\EcommerceTrademe\Includes\TradeMeAssignControllerHeader %>
<body>
    <div class="form-holder-trade-me">
        <div class="intro">
            <h1>$Title</h1>
            <% include Sunnysideup\EcommerceTrademe\Includes\TradeMeAssignControllerFilter %>
            <% include Sunnysideup\EcommerceTrademe\Includes\TradeMeAssignControllerOtherLinks %>
            <h4>
                $Explanation
            </h4>
        </div>
        <% include Sunnysideup\EcommerceTrademe\Includes\TradeMeAssignControllerPagination %>
        $Form
        <% include Sunnysideup\EcommerceTrademe\Includes\TradeMeAssignControllerPagination %>
        <% include Sunnysideup\EcommerceTrademe\Includes\TradeMeAssignControllerOtherLinks %>
    </div>
</body>
</html>

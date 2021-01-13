<% include Sunnysideup\EcommerceTrademe\IncludesTradeMeAssignControllerHeader %>
<body>
    <div class="form-holder-trade-me">
        <div class="intro">
            <h1>$Title</h1>
            <% include Sunnysideup\EcommerceTrademe\IncludesTradeMeAssignControllerFilter %>
            <% include Sunnysideup\EcommerceTrademe\IncludesTradeMeAssignControllerOtherLinks %>
            <h4>
                Below is a list of all product categories on the site with their associated TradeMe Category.
                <% if $Filter %>Filtered for <em>$Filter</em> ($FilterCount).<% end_if %>
            </h4>
        </div>
        <% include Sunnysideup\EcommerceTrademe\IncludesTradeMeAssignControllerPagination %>
        $Form
        <% include Sunnysideup\EcommerceTrademe\IncludesTradeMeAssignControllerPagination %>
        <% include Sunnysideup\EcommerceTrademe\IncludesTradeMeAssignControllerOtherLinks %>
    </div>
</body>
</html>

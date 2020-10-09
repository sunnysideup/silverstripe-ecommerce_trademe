<% include TradeMeAssignControllerHeader %>
<body>
    <div class="form-holder-trade-me">
        <div class="intro">
            <h1>$Title</h1>
            <h4>
                Below is a list of all product categories on the site with their associated TradeMe Category.
            </h4>
            <ul class="inline-links">
                <li>
                    Filter by setting:
                </li>
                <% loop FilterLinks %>
                <li><a href="$Link" class="$LinkingMode">$Title</a></li>
                <% end_loop %>
            </ul>
        </div>
        $Form
    </div>
</body>
</html>

<!doctype html>

<html lang="en">
<head>
    <% base_tag %>
    <title>$Title</title>
    <style>
    .form-holder-trade-me  {
        padding: 2vh 2vw;
    }
    .form-holder-trade-me h1,
    .form-holder-trade-me #Form_Form_error {
        text-align: center;
        position: fixed;
        top: 0;
        right: 0;
        left: 0;
        margin: 0;
        text-align: center;
        background-color: green;
    }
    .form-holder-trade-me fieldset {
        border: none!important;
    }
    .form-holder-trade-me .field {
        padding: 0.3vh 2vw ;
    }
    .form-holder-trade-me .Actions {
        background-color: green;
        position: fixed;
        bottom: 0;
        right: 0;
        left: 0;
        height: 50px;
        text-align: center;
    }
    .form-holder-trade-me .Actions input {
        width: 300px;
        height: 30px;
        padding: 10px;
        display: block;
        float: right;
    }
    .form-holder-trade-me .message {
        background-color: green;
        padding: 1vw;
        color: #fff;
        text-align: center;
    }

    .form-holder-trade-me span.description {
        float: right;
        display: block;
        margin-top: -1em;
    }
    </style>
</head>

<body>
    <div class="form-holder-trade-me">
        <h1>$Title</h1>
        $Form
    </div>
</body>
</html>

<!doctype html>

<html lang="en">
<head>
    <% base_tag %>
    <title>$Title</title>
    <style>
        h1 {
            text-align: center;
        }
        fieldset {
            border: none!important;
        }
        .field {
            padding: 0.3vh 2vw ;
        }
        .Actions input {
            width: 300px;
            height: 60px;
            display: block;
            margin: 0 auto;
        }
        .message {
            background-color: green;
            padding: 1vw;
            color: #fff;
            text-align: center;
        }
        .form-holder {
            padding: 2vh 2vw;
        }
        span.description {
            float: right;
            display: block;
            margin-top: -1em;
        }
    </style>
</head>

<body>
    <h1>$Title</h1>
    <div class="form-holder">
        $Form
    </div>
</body>
</html>

<!doctype html>

<html lang="en">
<head>
    <% base_tag %>

    <title>$Title</title>

    <style>
        body {
            font-family: sans-serif;
            background-color: lightgrey;
        }

        body a {
            color: green;
            text-decoration: none;
        }

        body a:visited {
            color: darkgreen;
        }

        /* Lines of each data point */
        .middleColumn {
            padding: 2px;
        }

        /* Main form found at bottom */
        .form-holder-trade-me  {
            padding: 2vh 2vw;
            margin-top: 60px;
        }

        .form-holder-trade-me h1,
        .form-holder-trade-me #Form_Form_error {
            top: 0;
        }

        .form-holder-trade-me fieldset {
            border: none!important;
        }

        .form-holder-trade-me .field {
            padding: 0.3vh 2vw ;
        }

        .form-holder-trade-me .Actions {
            bottom: 0;
        }

        /* Top sticky title */
        .form-holder-trade-me h1,
        .form-holder-trade-me #Form_Form_error,
        .form-holder-trade-me .Actions {
            text-align: center;
            position: fixed;
            color: lightgrey;
            right: 0;
            left: 0;
            margin: 0;
            background-color: black;
            padding: 0.5em;
        }

        /* Button found at bottom */
        .form-holder-trade-me .Actions input {
            width: 200px;
            height: 30px;
            /* padding: 10px; */
            display: inline-block;

            font-size: 1em;
            background-color: lightgrey;
            border-radius: 8px;
            /* box-shadow: inset 0 0 10px #000000; */
        }

        .form-holder-trade-me .message {
            background-color: darkgrey;
            padding: 1vw;
            color: #fff;
            text-align: center;
        }

        .form-holder-trade-me span.description {
            float: right;
            display: block;
            margin-top: -1em;

            /* color: purple; */
            /* background-color: purple; */
        }

        /* Pencil icon */
        .form-holder-trade-me span.description a {
            /* text-decoration: none; */
            font-size: 2em;
        }

    </style>
</head>

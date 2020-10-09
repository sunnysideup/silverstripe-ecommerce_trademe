<!doctype html>

<html lang="en">
<head>
    <% base_tag %>

    <title>$Title</title>

    <style>
        body {
            font-family: sans-serif;
            background-color: lightgrey;
            padding: 0;
            margin: 0;
        }


        /* Lines of each data point */

        /* Main form found at bottom */
        .form-holder-trade-me  {
            padding: calc(2vh + 2vw);
            margin-top: 60px;
        }

        .form-holder-trade-me h1,
        .form-holder-trade-me #Form_Form_error {
            top: 0;
        }

        .form-holder-trade-me fieldset {
            border: none!important;
            margin: 0;
            padding: 0;
        }

        .form-holder-trade-me .field {
            line-height: 1.7;
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
        .form-holder-trade-me #Form_Form_error {
            background-color: yellow!important;
            color: red!important;
            width: 200px;
            left: auto;
        }
        /* Button found at bottom */
        .form-holder-trade-me .Actions input {
            padding: 0 30px;
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
        form a {
            color: green;
            text-decoration: none;
        }

        form a:visited {
            color: darkgreen;
        }

        /* Pencil icon */
        .form-holder-trade-me span.description a {
            /* text-decoration: none; */
            font-size: calc(2vh + 2vw);
        }
        .inline-links {
            position: fixed;
            left: calc(2vh + 2vw);
            top: calc(1vh + 1vw);
        }
        .inline-links,
        .inline-links li,
        .inline-links li a {
            display: inline-block;
            margin: 0;
            padding: 0;
            color: #fff;
        }
        label.left {
            float: left;
            margin-right: calc(0.5vh + 0.5vw);
        }
        .field {
            clear: both;
        }
        .middleColumn {
            display: inline-block;
        }
        label.right {
            float: right;
        }
        .checkbox label.right {
            float: none;
        }
        a {text-decoration: underline}
        a.current, a:hover {text-decoration: none;color: orange!important;}
    </style>
</head>

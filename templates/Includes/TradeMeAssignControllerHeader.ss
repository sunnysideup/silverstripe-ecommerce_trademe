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
            color: #111;
        }

        a:link,
        a:visited {
            text-decoration: underline;
            color: #111;
        }
        a.current,
        a:hover {
            text-decoration: none;color: orange!important;
        }


        .form-holder-trade-me  {
            padding: calc(2vh + 2vw);
            margin-top: 60px;
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

        .form-holder-trade-me h1,
        .form-holder-trade-me #Form_Form_error {
            top: 0;
        }
        .form-holder-trade-me #Form_Form_error {
            background-color: yellow!important;
            color: red!important;
            width: 200px;
            left: auto;
        }

        /** inline links */
        .form-holder-trade-me .inline-links {
            position: fixed;
            top: calc(2vh + 2vw);
            left: 0;
            right: 0;
            display: block;
            padding: calc(0.5vh + 0.5vw);
            background-color: #555;
            text-align: center;
        }
        .form-holder-trade-me .inline-links li,
        .form-holder-trade-me .inline-links li a {
            display: inline-block;
            margin: 0;
            padding: 0;
            color: #fff;
        }

        /** form */
        .form-holder-trade-me fieldset {
            border: none!important;
            margin: 0;
            padding: 0;
        }

        .form-holder-trade-me .field {
            line-height: 1.7;
            clear: both;
        }

        .form-holder-trade-me .Actions {
            bottom: 0;
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
        .form-holder-trade-me form a {
            color: green;
            text-decoration: none;
        }

        .form-holder-trade-me form a:visited {
            color: darkgreen;
        }

        /* Pencil icon */
        .form-holder-trade-me span.description a {
            /* text-decoration: none; */
            font-size: calc(2vh + 2vw);
        }

        label.left {
            float: left;
            margin-right: calc(0.5vh + 0.5vw);
        }
        label.right {
            float: right;
        }
        .middleColumn {
            display: inline-block;
        }
        .checkbox label.right {
            float: none;
        }
        .form-holder-trade-me .optionset {
            margin: 0;
            padding: 0;
        }
        .form-holder-trade-me .optionset li {
            display: inline;
        }

    </style>
</head>

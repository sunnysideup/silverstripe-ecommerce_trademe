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
        .separator {
            content: '❱';
            display: inline-block;
            width: 0.7em;
            height: 0.7em;
            background-image: url(/resources/themes/base/images/icons/right-arrow.svg);
            background-position: 50%;
            background-repeat: no-repeat;
            background-size: 16px 16px;
        }

        /* Top sticky title */
        .form-holder-trade-me h1,
        .form-holder-trade-me #Form_Form_error,
        .form-holder-trade-me .Actions {
            text-align: center;
            position: fixed;
            color: #eee;
            right: 0;
            left: 0;
            margin: 0;
            background-color: black;
            padding: 0.5em;
        }

        .form-holder-trade-me h1,
        .form-holder-trade-me #Form_Form_error {
            top: 0;
            height: calc(2vh + 2vw);
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
        .pagination {
            text-align: right;
            padding: 5px;
            margin: 10px 0;
            background-color: #ccc;
        }
        .pagination span, .pagination a {
            border-radius: 50%;
            display: inline-block;
            padding: 5px;
            min-width: 1em;
            background-color: orange;
            text-align: center;
            margin-left: 0.5em;
        }
        .pagination span {
            background-color: #fff;
        }
        .pagination a.next, .pagination a.prev {
            background-color: transparent;
        }
        .pagination span:hover, .pagination a:hover {
            background-color: #fff;
        }
        .float-left {
            float: left;
            width: auto;
            margin-right: calc(0.5vh + 0.5vw)!important;
        }

    </style>
</head>

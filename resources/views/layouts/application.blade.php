<!DOCTYPE html>
<html lang="en">
<head>

    <!-- Title -->
    <title>@yield('title', 'SEEMICON 2025')</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <meta charset="UTF-8">
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta name="author" content="MKS" />

    <!-- Styles -->
    <link type="text/css" rel="stylesheet" href="/assets/plugins/materialize/css/materialize.min.css"/>
    <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="/assets/plugins/material-preloader/css/materialPreloader.min.css" rel="stylesheet">


    <!-- Theme Styles -->
    <link href="/assets/css/alpha.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/css/custom.css" rel="stylesheet" type="text/css"/>


    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="http://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="http://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>
<body>
@include('components.loader')
<div class="mn-content fixed-sidebar">
    @include('components.application-header')

    @yield('content')

<div class="left-sidebar-hover"></div>

<!-- Javascripts -->
<script src="/assets/plugins/jquery/jquery-2.2.0.min.js"></script>
<script src="/assets/plugins/materialize/js/materialize.min.js"></script>
<script src="/assets/plugins/material-preloader/js/materialPreloader.min.js"></script>
<script src="/assets/plugins/jquery-blockui/jquery.blockui.js"></script>
<script src="/assets/plugins/jquery-validation/jquery.validate.min.js"></script>
<script src="/assets/plugins/jquery-steps/jquery.steps.min.js"></script>
<script src="/assets/js/alpha.min.js"></script>
<script src="/assets/js/pages/form-wizard.js"></script>

</body>
</html>

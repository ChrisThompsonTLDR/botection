<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }}</title>

    @stack ('before-styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/4.1.2/cosmo/bootstrap.min.css" integrity="sha256-w8S2LUcn9W+xBRanLrLK7e8HCKEXBohp3kRTrCl0S1g=" crossorigin="anonymous" />
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    @stack ('after-styles')
    <style>
    .text-deleted,
    .text-deleted .text-muted {
        color: #E0E0E0 !important;
    }
    body {
        margin-bottom: 200px;
        font-family: 'Roboto', sans-serif;
    }
    </style>
</head>
<body>
@yield ('content')
</body>

@stack ('before-scripts')
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha256-xaF9RpdtRxzwYMWg4ldJoyPWqyDPCRD0Cv7YEEe6Ie8=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js" integrity="sha256-CfcERD4Ov4+lKbWbYqXD6aFM9M51gN4GUEtDhkWABMo=" crossorigin="anonymous"></script>
<script defer src="https://pro.fontawesome.com/releases/v5.1.1/js/all.js" integrity="sha384-cHcg4nvWPIGArJhEgL2F5e09Cn1GyPQpNYKbPatFCpDefCbezZjPA3PhLozKTZnv" crossorigin="anonymous"></script>
<script>
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
</script>
@stack ('after-scripts')
</html>

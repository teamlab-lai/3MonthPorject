<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        {{ get_title() }}
        {{ stylesheet_link('css/bootstrap.min.css') }}
        {{ stylesheet_link('css/bootstrap.rewrite.css') }}
        {{ stylesheet_link('css/ct-navbar.css') }}
        {{ stylesheet_link('css/pe-icon-7-stroke.css') }}
        {{ stylesheet_link('css/ladda-themeless.min.css') }}

        {{ assets.outputCss() }}
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

    </head>
    <body>
        {{ content() }}
        {{ javascript_include('js/jquery.min.js') }}
        {{ javascript_include('js/bootstrap.min.js') }}
        {{ javascript_include('js/utils.js') }}
        {{ javascript_include('js/ct-navbar.js') }}
        {{ javascript_include('js/spin.min.js') }}
        {{ javascript_include('js/ladda.min.js') }}
        {{ assets.outputJs() }}
    </body>
</html>
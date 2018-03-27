
        <div class="clear"></div>

        <footer class="text-center">
            {{ date('Y') }} &copy; Havas Worldwide Jakarta
        </footer>

        <script type="text/javascript" src="{{ env('APP_HOME_URL') }}/static/js/lib.js"></script>
        <script type="text/javascript" src="{{ env('APP_HOME_URL') }}/static/js/chosen.jquery.min.js"></script>
        <script type="text/javascript" src="{{ env('APP_HOME_URL') }}/static/js/admin-master.js"></script>
        <script type="text/javascript" src="{{ env('APP_HOME_URL') }}/static/js/admin-custom.js?t={{ time() }}"></script>
    </body>
</html>

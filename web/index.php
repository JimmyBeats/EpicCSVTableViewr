<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>The Epic CSV Table Viewr Demo</title>
    <meta name="description" content="The Epic CSV Table Viewr Demo">
    <meta name="author" content="James Beattie">
    <link
        rel="stylesheet"
        href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
        integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u"
        crossorigin="anonymous">
    <link
        rel="stylesheet"
        href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css"
        integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp"
        crossorigin="anonymous">
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/Dynatable/0.3.1/jquery.dynatable.min.css"
        integrity="sha256-lxcbK1S14B8LMgrEir2lv2akbdyYwD1FwMhFgh2ihls="
        crossorigin="anonymous" />
</head>
<body>

    <div class="container">

        <div>

            <h1>Epic CSV Tableviewr</h1>

            <table id="user-table" class="table table-bordered">
                <thead>
                    <th>
                        Last Name
                    </th>
                    <th>
                        First Name
                    </th>
                    <th>
                        Email
                    </th>
                    <th>
                        Role
                    </th>
                    <th>
                        Department
                    </th>
                </thead>
                <tbody>
                </tbody>
            </table>

        </div>

    </div>

    <!-- scripts - down the bottom to prevent blocking of page loading -->
    <script
        src="https://code.jquery.com/jquery-3.1.1.min.js"
        integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="
        crossorigin="anonymous"></script>
    <script
        src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
        integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
        crossorigin="anonymous"></script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/Dynatable/0.3.1/jquery.dynatable.min.js"
        integrity="sha256-/kLSC4kLFkslkJlaTgB7TjurN5TIcmWfMfaXyB6dVh0="
        crossorigin="anonymous"></script>
    <script>
        jQuery(function(){
            $.dynatableSetup({
                table: {
                    defaultColumnIdStyle: 'underscore'
                }
            });
            $('#user-table').dynatable({
                dataset: {
                    ajax: true,
                    ajaxUrl: '/data.php',
                    ajaxOnLoad: true,
                    records: []
                }
            });
        });
    </script>

</body>
</html>



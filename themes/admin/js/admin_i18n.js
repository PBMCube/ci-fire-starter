$(document).ready(function() {

    /**
     * Detect items per page change and send users back to page 1
     */
    $('select#limit').change(function() {
        var limit = $(this).val();
        var currentUrl = document.URL.split('?');
        var uriParams = "";
        var separator;

        if (currentUrl[1] != undefined) {
            var parts = currentUrl[1].split('&');

            for (var i = 0; i < parts.length; i++) {
                if (i == 0) {
                    separator = "?";
                } else {
                    separator = "&";
                }

                var param = parts[i].split('=');

                if (param[0] == 'limit') {
                    uriParams += separator + param[0] + "=" + limit;
                } else if (param[0] == 'offset') {
                    uriParams += separator + param[0] + "=0";
                } else {
                    uriParams += separator + param[0] + "=" + param[1];
                }
            }
        } else {
            uriParams = "?limit=" + limit;
        }

        // reload page
        window.location.href = currentUrl[0] + uriParams;
    });


    /**
     * Delete a user
     */
    $('.btn-delete-user').click(function() {
        window.location.href = "/admin/users/delete/" + $(this).attr('data-id');
    });

});
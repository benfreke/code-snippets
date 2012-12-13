jQuery(document).ready(function () {
    FB.init({
        appId:1234 // any valid app id
    });

    // .on() is valid from 1.7 onwards
    jQuery('facebookicon').on('click', function (e) {
        // Stop the link taking us anywhere
        e.preventDefault();

        FB.ui(
            {
                method:'feed',
                name:'name',
                link:'link to share',
                picture:'picture to share',
                caption:'caption (optional)',
                description:'description of the share'
            },
            function (response) {
                if (response && response.post_id) {
                    //alert('Post was published.');
                } else {
                    //alert('Post was not published.');
                }
            }
        )
    });

});


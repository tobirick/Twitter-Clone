$(function() {
    $('.search').on('keyup', function() {
        $('.search-result').empty();
        var search = $(this).val();
        $.ajax({
            url: '/search/search-user',
            data: {
                search: search
            },
            error: function(data) {
                console.log("test");
            },
            success: function(data) {
                $('.search-result').html(data);
            },
            type: 'GET'
        });
    });
});
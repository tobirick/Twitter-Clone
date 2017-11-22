$(function() {
    $('.follow-btn').on('click', function() {
        var user_id = $(this).parent().attr('data-user-id');
        var token = $('.csrf_token').val();
        var button = $(this);
        $.ajax({
            url: '/follows/follow-user',
            data: {
                user_id: user_id,
                csrf_token: token
            },
            dataType:"json",
            error: function(data) {
                console.log(data);
            },
            success: function(data) {
                $('.csrf_token').each(function() {
                   $(this).val(data.csrf_token);
                });
                if(data.follow == 'unfollow') {
                    button.html('Unfollow');
                } else {
                    button.html('Follow');
                }
            },
            type: 'POST'
        });
    });
});
$(document).ready(function () {
    // load messages in popup
    $(document).on('click', '#showMessages', function() {
       var getMessages = 1;
       var token = $('.csrf_token').val();
       $.ajax({
           url: '/messages/recent-messages',
           type: 'POST',
           dataType:"json",
           data: {
               showMessage: getMessages,
               csrf_token: token
           },
           error: function(data) {
               console.log(data);
           },
           success: function(data) {
               $('.csrf_token').each(function() {
                   $(this).val(data.csrf_token);
               });
               // display popup
               $('.modal').html(data.html);
           },
       });
   });

   // load converstaion in popup
    $(document).on('click', '#showConversation', function() {
        var from_id = $(this).attr('data-user-id');
        var token = $('.csrf_token').val();
        $.ajax({
            url: '/messages/messages-conversation',
            type: 'POST',
            dataType:"json",
            data: {
                from_id: from_id,
                csrf_token: token
            },
            error: function(data) {
                console.log(data);
            },
            success: function(data) {
                $('.csrf_token').each(function() {
                    $(this).val(data.csrf_token);
                });
                // display popup
                $('.modal').html(data.html);
            },
        });
    });

    // send message
    $(document).on('click', '#sendMessage', function() {
        var to_id = $(this).attr('data-user-id');
        var msg = $('#inputMessage').val();
        var token = $('.csrf_token').val();
        $.ajax({
            url: '/messages/send-message',
            type: 'POST',
            dataType:"json",
            data: {
                to_id: to_id,
                msg: msg,
                csrf_token: token
            },
            error: function(data) {
                console.log(data);
            },
            success: function(data) {
                $('.csrf_token').each(function() {
                    $(this).val(data.csrf_token);
                });
                // display popup
                //getMessages();
                $('#inputMessage').val('');
            },
        });
    });
});
$(document).ready(function () {
   $('#messagePopup').on('click', function() {
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
});
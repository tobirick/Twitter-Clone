$(document).ready(function () {
    // Popup open/close
    $('.open-popup').on('click', function() {
        if($('body').hasClass('modal-enabled')) {
            $('body').removeClass('modal-enabled');
        } else {
            $('body').addClass('modal-enabled');
        }
    });

    $(document).on('click', '.close-modal', function() {
        $('body').removeClass('modal-enabled');
        $('.modal').html('');
    });
});
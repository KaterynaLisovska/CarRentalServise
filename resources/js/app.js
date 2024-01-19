import './bootstrap';

$(document).ready(function () {
    $('.formFilter').on("submit", function (event) {
        event.preventDefault();
        event.stopPropagation();

        $('#preloader').removeClass('hidden').addClass('flex');

        let formData = $(this).serialize(); // Use $(this) to reference the form being submitted
        $.ajax({
            url: '/',
            method: 'POST',
            data: formData,
            success: function (result) {
                $('.list').html(result.html);
                $('#preloader').removeClass('flex').addClass('hidden');
            }
        });
    });
});


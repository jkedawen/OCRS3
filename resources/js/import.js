$(document).ready(function () {
    $('#import-button').click(function (e) {
        e.preventDefault();

        let formData = new FormData($('form')[0]);

        $.ajax({
            url: '{{ route('import') }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (data) {
                // Update the view with the imported data
                $('#imported-data-container').html(data);
            },
            error: function () {
                alert('Error importing data.');
            }
        });
    });
});
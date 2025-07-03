$(document).ready(function() {
    $('#feedbackForm').submit(function(e) {
        e.preventDefault();
        
        // Validate form
        if (!this.checkValidity()) {
            e.stopPropagation();
            this.classList.add('was-validated');
            return;
        }
        
        // Collect form data
        const formData = {
            name: $('#name').val(),
            email: $('#email').val(),
            rating: $('input[name="rating"]:checked').val(),
            comments: $('#comments').val()
        };
        
        // Send AJAX request
        $.ajax({
            type: 'POST',
            url: 'submit_feedback.php',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#responseMessage').html(
                        '<div class="alert alert-success">' + response.message + '</div>'
                    );
                    $('#feedbackForm')[0].reset();
                    $('#feedbackForm').removeClass('was-validated');
                    $('.star-rating label').removeClass('active');
                } else {
                    $('#responseMessage').html(
                        '<div class="alert alert-danger">' + response.message + '</div>'
                    );
                }
                
                // Hide messages after 5 seconds
                setTimeout(function() {
                    $('#responseMessage').empty();
                }, 5000);
            },
            error: function(xhr, status, error) {
                $('#responseMessage').html(
                    '<div class="alert alert-danger">An error occurred. Please try again.</div>'
                );
            }
        });
    });
});
<?php require_once 'includes/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/star-rating.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        .card {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card-header {
            background: linear-gradient(135deg, #3a7bd5 0%, #00d2ff 100%);
        }
        .form-control:focus {
            border-color: #3a7bd5;
            box-shadow: 0 0 0 0.25rem rgba(58, 123, 213, 0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg, #3a7bd5 0%, #00d2ff 100%);
            border: none;
            padding: 10px 20px;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #2c65c4 0%, #00b7eb 100%);
        }
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-lg">
                    <div class="card-header text-white">
                        <h3 class="text-center mb-0">We Value Your Feedback</h3>
                    </div>
                    <div class="card-body p-4">
                        <form id="feedbackForm" novalidate>
                            <div class="mb-3">
                                <label for="name" class="form-label">Your Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required>
                                <div class="invalid-feedback">Please provide your name</div>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" required>
                                <div class="invalid-feedback">Please provide a valid email</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Rating <span class="text-danger">*</span></label>
                                <div class="star-rating">
                                    <input type="radio" id="5-stars" name="rating" value="5" aria-label="5 stars - Excellent" aria-checked="false" />
                                    <label for="5-stars" tabindex="0"></label>
                                    
                                    <input type="radio" id="4-stars" name="rating" value="4" aria-label="4 stars - Good" aria-checked="false" />
                                    <label for="4-stars" tabindex="0"></label>
                                    
                                    <input type="radio" id="3-stars" name="rating" value="3" aria-label="3 stars - Average" aria-checked="false" />
                                    <label for="3-stars" tabindex="0"></label>
                                    
                                    <input type="radio" id="2-stars" name="rating" value="2" aria-label="2 stars - Below Average" aria-checked="false" />
                                    <label for="2-stars" tabindex="0"></label>
                                    
                                    <input type="radio" id="1-star" name="rating" value="1" aria-label="1 star - Poor" aria-checked="false" required />
                                    <label for="1-star" tabindex="0"></label>
                                    
                                    <div class="invalid-feedback">Please select a rating</div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="comments" class="form-label">Comments</label>
                                <textarea class="form-control" id="comments" name="comments" rows="3" placeholder="Your optional feedback..."></textarea>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <span class="submit-text">Submit Feedback</span>
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                </button>
                            </div>
                        </form>
                        <div id="responseMessage" class="mt-3"></div>
                    </div>
                </div>
                <div class="text-center mt-4 text-muted">
                    <small>Your feedback helps us improve our services</small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/star-rating.js"></script>
    <script>
    $(document).ready(function() {
        // Form submission with AJAX
        $('#feedbackForm').submit(function(e) {
            e.preventDefault();
            
            // Validate form
            if (!this.checkValidity()) {
                e.stopPropagation();
                this.classList.add('was-validated');
                return;
            }
            
            // Show loading state
            const $btn = $(this).find('button[type="submit"]');
            $btn.prop('disabled', true);
            $btn.find('.submit-text').addClass('d-none');
            $btn.find('.spinner-border').removeClass('d-none');
            
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
                            `<div class="alert alert-success alert-dismissible fade show">
                                ${response.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>`
                        );
                        $('#feedbackForm')[0].reset();
                        $('#feedbackForm').removeClass('was-validated');
                        $('.star-rating input').prop('checked', false).attr('aria-checked', 'false');
                    } else {
                        $('#responseMessage').html(
                            `<div class="alert alert-danger alert-dismissible fade show">
                                ${response.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>`
                        );
                    }
                },
                error: function(xhr, status, error) {
                    $('#responseMessage').html(
                        `<div class="alert alert-danger alert-dismissible fade show">
                            An error occurred. Please try again later.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>`
                    );
                },
                complete: function() {
                    // Reset button state
                    $btn.prop('disabled', false);
                    $btn.find('.submit-text').removeClass('d-none');
                    $btn.find('.spinner-border').addClass('d-none');
                }
            });
        });
    });
    </script>
</body>
</html>
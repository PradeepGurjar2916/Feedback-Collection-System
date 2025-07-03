$(document).ready(function() {
    // Initialize star rating
    const $starRating = $('.star-rating');
    
    $starRating.on('click', 'input', function() {
      const $this = $(this);
      $this.closest('.star-rating').find('.invalid-feedback').hide();
      
      // Set aria-checked attributes
      $starRating.find('input').attr('aria-checked', 'false');
      $this.attr('aria-checked', 'true');
    });
  
    // Hover effects
    $starRating.on('mouseenter', 'label', function() {
      $(this).addClass('hover');
      $(this).prevAll('label').addClass('hover');
    }).on('mouseleave', 'label', function() {
      $starRating.find('label').removeClass('hover');
    });
  
    // Keyboard accessibility
    $starRating.on('keydown', 'input', function(e) {
      const $current = $(this);
      if (e.key === ' ' || e.key === 'Enter') {
        e.preventDefault();
        $current.prop('checked', true).trigger('click');
      } else if (e.key === 'ArrowRight' || e.key === 'ArrowUp') {
        e.preventDefault();
        $current.next('input').focus();
      } else if (e.key === 'ArrowLeft' || e.key === 'ArrowDown') {
        e.preventDefault();
        $current.prev('input').focus();
      }
    });
  
    // Form validation
    $('form').on('submit', function() {
      const $ratingInput = $starRating.find('input[name="rating"]');
      if (!$ratingInput.is(':checked')) {
        $starRating.find('.invalid-feedback').show();
        return false;
      }
      return true;
    });
  });
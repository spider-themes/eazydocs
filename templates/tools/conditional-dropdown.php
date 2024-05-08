<?php
$opt                     = get_option( 'eazydocs_settings' );
$is_conditional_dropdown = $opt['is_conditional_dropdown'] ?? '';
$condition_options       = $opt['condition_options'] ?? '';

if ( $is_conditional_dropdown == '1' && ! empty( $condition_options ) ) :
    wp_enqueue_style('font-awesome-5');
	?>
    <select id="condition_options" name="condition_options" class="vodiapicker ezd-d-none">
		<?php
		foreach ( $condition_options as $option ) {
			?>
            <option value="<?php echo sanitize_title( $option['title'] ) ?>" data-content=" <?php echo esc_attr( $option['icon'] ) ?>">
				<?php echo sanitize_title( $option['title'] ) ?>
            </option>
			<?php
		}
		?>
    </select>
    <div class="lang-select">
        <button class="ezd_btn_select" value=""></button>
        <div class="ezd_b">
            <ul id="ezd_a" class="ezd-list-unstyled"></ul>
        </div>
    </div>

    <script>
      (function($) {
        'use strict';
        $(document).ready(function() {

          var updateEzdConVisibility = function(value) {
            var selector = '.ezd-con-' + value;
            if ($(selector).length > 0) {
              // Hide all elements with ezd-con- class only if matching elements are found
              $('[class^="ezd-con-"]').hide();
              $(selector).show(); // Show only the elements with the class matching the selected option
            }
          };

          if ($('.vodiapicker option').length > 0) {
            var langArray = [];
            $('.vodiapicker option').each(function() {
              var icon = $(this).attr('data-content');
              var text = this.innerText;
              var value = $(this).val(); // Get the value of the option
              var item = '<li data-value="' + value + '"> <i class="' + icon + '"></i> <span>' + text + '</span></li>';
              langArray.push(item);
            });

            $('#ezd_a').html(langArray);

            // Set the button value to the first element of the array
            var firstValue = $('.vodiapicker option').first().val();
            $('.ezd_btn_select').html(langArray[0]);
            $('.ezd_btn_select').attr('value', firstValue);
            $('#ezd_a li').first().addClass('active');
            updateEzdConVisibility(firstValue); // Update visibility on initial load

            // Change button stuff on click
            $('#ezd_a li').click(function() {
              var icon = $(this).find('i').attr('class');
              var value = $(this).data('value'); // Get the value from data attribute
              var text = this.innerText;
              var item = '<li> <i class="' + icon + '"></i> <span>' + text + '</span></li>';
              $('.ezd_btn_select').html(item);
              $('.ezd_btn_select').attr('value', value);
              $('.ezd_b').toggleClass('ezd_show');
              $('#ezd_a li').removeClass('active');
              $(this).addClass('active');
              updateEzdConVisibility(value); // Update visibility based on selected value

              // Hide / Show Toc items based on conditional dropdowns
              $('[class^="ezd-con-"]').each(function(){              
                  var ezd_con_id    = $(this).parent().attr('id');
                    if (ezd_con_id !=  null ) {

                        var ezd_con_inner_style = $('#'+ezd_con_id+' > span').attr('style');                        
                        var escapedHref = $.escapeSelector('#'+ezd_con_id);

                        // if has display none with this innerStyle 
                        if (!ezd_con_inner_style.includes('display: none;')) {
                          $('.toc_right ul li a[href="' + escapedHref + '"]').css('display', 'block');
                        } else {                          
                          $('.toc_right ul li a[href="' + escapedHref + '"]').css('display', 'none');
                        }
                    }
              });
              
            });

            $('.ezd_btn_select').click(function() {
              $('.ezd_b').toggleClass('ezd_show');
            });
          }
          
          // Hide / Show Toc items based on conditional dropdowns
          $('[class^="ezd-con-"]').each(function(){          
              var ezd_con_id   = $(this).parent().attr('id');
              if ( ezd_con_id !=  null ) {
                  var ezd_con_inner_style  = $('#'+ezd_con_id+' > span').attr('style');                
                  if (ezd_con_inner_style && ezd_con_inner_style.includes('display: none;')) {

                    var escapedHref = $.escapeSelector('#'+ezd_con_id);
                    $('.toc_right ul li a[href="' + escapedHref + '"]').css('display', 'none');    

                 }
              }
          });

        });
      })(jQuery);
    </script>
<?php
endif;
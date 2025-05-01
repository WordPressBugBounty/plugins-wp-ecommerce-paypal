jQuery(document).ready(function($) {
    // Create and append the survey modal
    var surveyHTML = `
        <div id="wpecpp-deactivation-survey" style="display: none;">
            <div class="wpecpp-survey-content">
                <h2>${wpecppDeactivationSurvey.strings.title}</h2>
                <p>${wpecppDeactivationSurvey.strings.description}</p>
                <form id="wpecpp-deactivation-form">
                    <div class="wpecpp-survey-options">
                        ${Object.entries(wpecppDeactivationSurvey.deactivationOptions).map(([key, value]) => `
                            <label>
                                <input type="radio" name="deactivation_reason" value="${key}">
                                ${value}
                            </label>
                            ${key === 'found_better' ? `<div class="wpecpp-additional-field" data-for="found_better" style="display: none;">
                                <textarea name="user-reason" class="" rows="6" style="border-spacing: 0; width: 100%; clear: both; margin: 0;" placeholder="${wpecppDeactivationSurvey.strings.betterPluginQuestion}"></textarea>
                            </div>` : ''}
                            ${key === 'not_working' ? `<div class="wpecpp-additional-field" data-for="not_working" style="display: none;">
                                <textarea name="user-reason" class="" rows="6" style="border-spacing: 0; width: 100%; clear: both; margin: 0;" placeholder="${wpecppDeactivationSurvey.strings.notWorkingQuestion}"></textarea>
                            </div>` : ''}
                        `).join('')}
                    </div>
                    <div id="wpecpp-other-reason" style="display: none;">
                        <textarea name="user-reason" class="" rows="6" style="border-spacing: 0; width: 100%; clear: both; margin: 0;" placeholder="${wpecppDeactivationSurvey.strings.otherPlaceholder}"></textarea>
                    </div>
                    <div id="wpecpp-error-notice" class="notice notice-error" style="display: none; margin: 10px 0;">
                        <p>${wpecppDeactivationSurvey.strings.errorRequired}</p>
                    </div>
                    <div class="wpecpp-survey-buttons" style="display: flex; justify-content: space-between; margin-top: 20px;">
                        <div>
                            <button type="button" class="button button-secondary" id="wpecpp-skip-survey">${wpecppDeactivationSurvey.strings.skipButton}</button>
                        </div>
                        <div>
                            <button type="button" class="button button-secondary" id="wpecpp-cancel-survey">${wpecppDeactivationSurvey.strings.cancelButton}</button>
                            <button type="submit" class="button button-primary">${wpecppDeactivationSurvey.strings.submitButton}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    `;

    $('body').append(surveyHTML);

    // Show survey when deactivation link is clicked
    $(document).on('click', 'a[href*="action=deactivate&plugin=wp-ecommerce-paypal"]', function(e) {
        e.preventDefault();
        $('#wpecpp-deactivation-survey').show();
    });

    // Handle escape key
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && $('#wpecpp-deactivation-survey').is(':visible')) {
            $('#wpecpp-deactivation-survey').hide();
        }
    });

    // Handle cancel button
    $('#wpecpp-cancel-survey').on('click', function() {
        $('#wpecpp-deactivation-survey').hide();
    });

    // Handle radio button changes
    $('input[name="deactivation_reason"]').on('change', function() {
        var selectedValue = $(this).val();
        
        // Hide all additional fields first
        $('.wpecpp-additional-field').hide();
        $('#wpecpp-other-reason').hide();
        $('#wpecpp-error-notice').hide();
        
        // Remove error styling from all textareas
        $('textarea[name="user-reason"]').css('border-color', '');
        
        // Show relevant field based on selection
        if (selectedValue === 'other') {
            $('#wpecpp-other-reason').show();
        } else if (selectedValue === 'found_better' || selectedValue === 'not_working') {
            $(`.wpecpp-additional-field[data-for="${selectedValue}"]`).show();
        }
    });

    // Handle textarea input to remove error styling
    $('textarea[name="user-reason"]').on('input', function() {
        $(this).css('border-color', '');
        $('#wpecpp-error-notice').hide();
    });

    // Handle skip button
    $('#wpecpp-skip-survey').on('click', function() {
        window.location.href = $('a[href*="action=deactivate&plugin=wp-ecommerce-paypal"]').attr('href');
    });

    // Handle form submission
    $('#wpecpp-deactivation-form').on('submit', function(e) {
        e.preventDefault();
        
        var reason = $('input[name="deactivation_reason"]:checked').val();
        var additionalReason = '';
        var $textarea = null;
        
        // First check if any reason is selected
        if (!reason) {
            $('#wpecpp-error-notice').show().find('p').text('Error: Please select an option.');
            return;
        }
        
        // Get the appropriate additional reason based on the selected option
        if (reason === 'other') {
            $textarea = $('#wpecpp-other-reason textarea');
            additionalReason = $textarea.val();
        } else if (reason === 'found_better') {
            $textarea = $('.wpecpp-additional-field[data-for="found_better"] textarea');
            additionalReason = $textarea.val();
        } else if (reason === 'not_working') {
            $textarea = $('.wpecpp-additional-field[data-for="not_working"] textarea');
            additionalReason = $textarea.val();
        }
        
        // Hide any existing error notice
        $('#wpecpp-error-notice').hide();
        
        // Remove error styling from all textareas
        $('textarea[name="user-reason"]').css('border-color', '');
        
        // Validate required fields only if they should be filled in
        if ((reason === 'other' || reason === 'found_better' || reason === 'not_working') && !additionalReason.trim()) {
            $('#wpecpp-error-notice').show().find('p').text('Error: Please complete the required field.');
            if ($textarea) {
                $textarea.css('border-color', '#dc3232');
            }
            return;
        }
        
        // If validation passes, proceed with the AJAX submission
        $.ajax({
            url: 'https://wpplugin.org/wp-json/wpplugin/v1/deactivation-survey',
            method: 'POST',
            data: {
                plugin_slug: 'wp-ecommerce-paypal',
                plugin_version: wpecppDeactivationSurvey.pluginVersion,
                reason: reason,
                additional_reason: additionalReason
            },
            success: function() {
                window.location.href = $('a[href*="action=deactivate&plugin=wp-ecommerce-paypal"]').attr('href');
            },
            error: function() {
                window.location.href = $('a[href*="action=deactivate&plugin=wp-ecommerce-paypal"]').attr('href');
            }
        });
    });
}); 
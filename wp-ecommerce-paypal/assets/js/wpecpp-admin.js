(function( $ ) {
    'use strict';
    $( function() {
        $('.wpecpp-stripe-connect-notice, .wpecpp-ppcp-connect-notice').on('click', '.notice-dismiss', function(){
            const $notice = $(this).parent('.notice.is-dismissible');
            const dismiss_url = $notice.attr('data-dismiss-url');
            if (dismiss_url) {
                $.get(dismiss_url);
            }
        });

        $('#wpecpp-stripe-connect-table').on('change', 'input[name="mode_stripe"]', function(){
            const val = parseInt($(this).val());
            if (val !== 1 && val !== 2) return false;

            $('#stripe-connection-status-html').css({'opacity': 0.5});
            $.post(wpecpp.ajaxUrl, {
                action: 'wpecpp_stripe_connect_mode_change',
                nonce: wpecpp.nonce,
                val: val
            }, function(response){
                if (response.data.statusHtml) {
                    $('#stripe-connection-status-html').html(response.data.statusHtml).css({'opacity': 1});
                }
            });
        });

        function setOnboardingUrl() {
            const country = $('#ppcp-country').val(),
                acceptCards = $('#ppcp-accept-cards').is(':checked'),
                sandbox = $('#ppcp-sandbox').is(':checked'),
                $onboardingStartBtn = $('#ppcp-onboarding-start-btn'),
                onboardingUrl = $onboardingStartBtn.attr('href').split('?'),
                onboardingParams = new URLSearchParams(onboardingUrl[1] || '');

            onboardingParams.set('country', country);

            if (acceptCards) {
                onboardingParams.set('accept-cards', '1');
            } else {
                onboardingParams.delete('accept-cards');
            }

            if (sandbox) {
                onboardingParams.set('sandbox', '1');
            } else {
                onboardingParams.delete('sandbox');
            }

            onboardingUrl[1] = onboardingParams.toString();
            $onboardingStartBtn.attr('href', onboardingUrl.join('?'));
        }
        $(document).on('mousedown touchstart', '.ppcp-onboarding-start', function(){
            $('#ppcp-sandbox').prop('checked', $(this).attr('data-connect-mode') === 'sandbox');
            setOnboardingUrl();
        });
        $(document).on('change', '#ppcp-country', function(){
            const val = $(this).val(),
                $acceptCardsInput = $('#ppcp-accept-cards'),
                $acceptCardsLabel = $acceptCardsInput.parents('label');

            if (val === 'other') {
                $acceptCardsInput
                    .attr('disabled', 'disabled')
                    .prop('checked', false).change();
                $acceptCardsLabel
                    .addClass('ppcp-disabled')
                    .attr('title', $acceptCardsLabel.attr('data-title'));
            } else {
                $acceptCardsInput.removeAttr('disabled');
                $acceptCardsLabel.removeClass('ppcp-disabled').removeClass('title');
            }

            setOnboardingUrl();
        });
        $(document).on('change', '#ppcp-accept-cards, #ppcp-sandbox', function(){
            setOnboardingUrl();
        });
        $(document).on('click', '#ppcp-onboarding-start-btn, #ppcp-setup-account-close-btn', function(e){
            $(this).parents('#TB_window').find('#TB_closeWindowButton').click();
        });

        $(document).on('click', '#ppcp-disconnect', function(e){
            e.preventDefault();

            if (!confirm('Are you sure?')) return false;

            const $this = $(this),
                $ppcpStatusTable = $('#ppcp-status-table');

            if ($this.hasClass('processing')) return false;
            $this.addClass('processing');

            $ppcpStatusTable.css({'opacity': 0.5});

            $.post(wpecpp.ajaxUrl, {
                action: 'wpecpp-ppcp-disconnect',
                nonce: wpecpp.nonce
            }, function(response){
                $this.removeClass('processing');
                $ppcpStatusTable.css({'opacity': 1});

                if (response.success) {
                    $ppcpStatusTable.html(response.data.statusHtml);
                } else {
                    const message = response.data && response.data.message ?
                        response.data.message :
                        'An unexpected error occurred. Please reload the page and try again.';
                    alert(message);
                }
            });

            return false;
        });

    });
})(jQuery);
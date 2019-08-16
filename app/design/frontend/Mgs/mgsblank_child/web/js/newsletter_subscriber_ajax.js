require.config({
    deps: [
        'jquery'
    ],
    callback: function ($) {
        var form = $('form.subscribe');
        var form_footer = $('form.subscribe-footer');

        form.submit(function(e) {
            if(form.validation('isValid')){
                var email = $("#newsletter").val();
                var url = form.attr('action');
                var loadingMessage = $('#loading-message');

                if(loadingMessage.length == 0) {
                    form.find('.control').after('<div id="loading-message" class="bg-info text-info" style="display:none;">&nbsp;</div>');
                    var loadingMessage = $('#loading-message');
                }

                e.preventDefault();
                try{
                    loadingMessage.html('Submitting...').show();
                    $.ajax({
                        url: url,
                        dataType: 'json',
                        type: 'POST',
                        data: {email: email},
                        success: function (data){
                            if(data.status != "ERROR"){
                            	$('#loading-message').removeClass('bg-info text-info');
                            	$('#loading-message').addClass('bg-success text-success');
                                $('#newsletter').val('');
                            }
                            loadingMessage.html(data.msg);
                        },
                        complete: function(){
                            setTimeout(function(){
                                loadingMessage.hide();
                            },5000);
                        }
                    });
                } catch (e){
                    loadingMessage.html(e.message);
                }
            }
            return false;
        });
        
        form_footer.submit(function(e) {
            if(form_footer.validation('isValid')){
                var email = $("#newsletter-footer").val();
                var url = form_footer.attr('action');
                var loadingMessage = $('#loading-message-footer');

                if(loadingMessage.length == 0) {
                    form_footer.find('.control').append('<div id="loading-message-footer" class="bg-info text-info" style="display:none;">&nbsp;</div>');
                    var loadingMessage = $('#loading-message-footer');
                }

                e.preventDefault();
                try{
                    loadingMessage.html('Submitting...').show();
                    $.ajax({
                        url: url,
                        dataType: 'json',
                        type: 'POST',
                        data: {email: email},
                        success: function (data){
                            if(data.status != "ERROR"){
                            	$('#loading-message-footer').removeClass('bg-info text-info');
                            	$('#loading-message-footer').addClass('bg-success text-success');
                                $('#newsletter-footer').val('');
                            }
                            loadingMessage.html(data.msg);
                        },
                        complete: function(){
                            setTimeout(function(){
                                loadingMessage.hide();
                            },5000);
                        }
                    });
                } catch (e){
                    loadingMessage.html(e.message);
                }
            }
            return false;
        });

    }
})
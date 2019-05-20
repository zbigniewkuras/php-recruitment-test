$(document).ready(function(){
    $(':checkbox').on('change', function() {
        var $this = $(this);
        var varnishId, websiteId;
        varnishId = parseInt($this.data('varnish-id'));
        websiteId = parseInt($this.data('website-id'));
        if ($this.prop('checked')) {
            callAjax('varnish/link', varnishId, websiteId);
        } else {
            callAjax('varnish/unlink', varnishId, websiteId);
        }
        
    });
    function callAjax(url, varnishId, websiteId) {
        $.ajax({
            url: url,
            method: 'POST',
            dataType: 'json',
            data: {varnish_id: varnishId, website_id: websiteId},
            success: function(result){
                console.log(result);
                var message = '<p class="bg-info">' + result.message + '</p>';
                $('body > .container').prepend(message);
            }
        });
    }
});
(function($) {       
$.extend({
    postJSON: function(url,data,callback)
    {
        return $.ajax({
            cache: false,
            data: data,
            dataType: 'json',
            success: function(data,textStatus)
            {
                if(callback)
                {
                    callback(data);
                }
            },
            type: 'POST',
            url: url
        });
    }
  }); 
})(jQuery);    
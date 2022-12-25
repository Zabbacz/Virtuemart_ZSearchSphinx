 function __highlight(s, t) {
    var matcher = new RegExp("(" + jQuery.ui.autocomplete.escapeRegex(t) + ")", "ig");
    return s.replace(matcher, "<strong>$1</strong>");
}
jQuery(document).ready(
	function() {
	    jQuery("#suggest").autocomplete(
                        {
                        source : function(request, response) 
                            {
                                jQuery.ajax(
                                {
                                    url : './index.php',
                                    dataType : 'json',
                                    type   : 'GET',
                                    data : 
                                    {
                                        option : 'com_ajax',
                                        module : 'zsearchsphinx',
                                        format : 'raw',
                                        term : request.term
                                    },
                       success : function(data) {
				    response(jQuery.map(data, function(item) {
					return {
					    label : __highlight(item.label,
						    request.term),
					    value : item.label
					};
				    }));
				}
			    });
			},
			minLength : 3,
			select : function(event, ui) {
			    jQuery('#searchbutton').submit();
			}
		    }).keydown(function(e) {
		if (e.keyCode === 13) {
		    jQuery("#search_form").trigger('submit');
		}
	    }).data("autocomplete")._renderItem = function(ul, item) {
		return jQuery("<li class='sphinx_search'></li>").data("item.autocomplete", item).append(
			jQuery("<a></a>").html(item.label)).appendTo(ul);
	    };
            
	});
   
        
            
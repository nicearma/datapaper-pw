jQuery(document).ready(
        
        function() {
            jQuery("#tabs").tabs();/*
            jQuery( "#dialog" ).dialog({
    autoOpen: false,
    show: "blind",
    hide: "explode"
});*/

jQuery(document).on('click','.auto-search',function(){
    auto_search(this);
});

        });

(function($) {
                    return $.fn.serializeObject = function() {
                        var json, patterns, push_counters,
                                _this = this;
                        json = {};
                        push_counters = {};
                        patterns = {
                            validate: /^[a-zA-Z_][a-zA-Z0-9_]*(?:\[(?:\d*|[a-zA-Z0-9_]+)\])*$/,
                            key: /[a-zA-Z0-9_]+|(?=\[\])/g,
                            push: /^$/,
                            fixed: /^\d+$/,
                            named: /^[a-zA-Z0-9_]+$/
                        };
                        this.build = function(base, key, value) {
                            base[key] = value;
                            return base;
                        };
                        this.push_counter = function(key) {
                            if (push_counters[key] === void 0) {
                                push_counters[key] = 0;
                            }
                            return push_counters[key]++;
                        };
                        $.each($(this).serializeArray(), function(i, elem) {
                            var k, keys, merge, re, reverse_key;
                            if (!patterns.validate.test(elem.name)) {
                                return;
                            }
                            keys = elem.name.match(patterns.key);
                            merge = elem.value;
                            reverse_key = elem.name;
                            while ((k = keys.pop()) !== void 0) {
                                if (patterns.push.test(k)) {
                                    re = new RegExp("\\[" + k + "\\]$");
                                    reverse_key = reverse_key.replace(re, '');
                                    merge = _this.build([], _this.push_counter(reverse_key), merge);
                                } else if (patterns.fixed.test(k)) {
                                    merge = _this.build([], k, merge);
                                } else if (patterns.named.test(k)) {
                                    merge = _this.build({}, k, merge);
                                }
                            }
                            return json = $.extend(true, json, merge);
                        });
                        return json;
                    };
                })(jQuery);
                
function selectAll(){
    console.log('check');
    jQuery('input[type="checkbox"]').each(function(){
        jQuery(this).attr("checked",true);
    });
}

function selectCheck(obj){
    $(obj).find('input[type="checkbox"]').each(function(){
         jQuery(this).attr("checked",true);
    });
}

function deselectAll(toggle){
    console.log('uncheck');
    jQuery('input[type="checkbox"]').each(function(){
            jQuery(this).removeAttr('checked');
    });
}

/*
function make_modal(information) {
    jQuery('#unique').modal('show');
    jQuery("#unique .modal-header").html('');
    jQuery("#unique .modal-body").html('');
    for(action in information){
         jQuery("#unique #action-modal").html('');
        jQuery("#unique #action-modal").html(action.name);
        jQuery("body").off('click', '#modal-action');
        jQuery("#unique").on('click', '#action-modal',action, function() {
        action.action();
    });
    
    }
 
    jQuery("#unique .modal-header").html(information.header);
    jQuery("#unique .modal-body").html(information.body);
    // jQuery("#unique .modal-footer").html('');
   
}
*/
function DP_search_sparql2(squery,uri_sparql) {

   // escape(jQuery('textarea[data-source="' + jQuery(obj).attr('data-source') + '"]').val())
   squery = 'query=' +escape( squery) + '&output=json';

   //jQuery('input[data-source="' + jQuery(obj).attr('data-source') + '"]').val()
   return jQuery.ajax({
        url: uri_sparql,
        type: 'get',
        data: squery,
        dataType: 'json'
    });
}


function DP_ajax_server(data){
   return jQuery.ajax({
        url: "admin-ajax.php",
        type: 'post',
        data: data,
        dataType: 'json'
    });
}


 function auto_search(obj) {
     var original=obj;
     console.log(jQuery.parseJSON(jQuery( original ).attr('data-object')));
    jQuery( obj ).autocomplete({
      minLength: 0,
      source: jQuery.parseJSON(jQuery( original ).attr('data-object')),
      focus: function( event, ui ) {
        jQuery( original ).val( ui.item.name );
        return false;
      },
      select: function( event, ui ) {
        jQuery( original ).val( ui.item.name );
        
        jQuery('input[name="'+jQuery( original ).attr('data-value')+'"]').val(ui.item.id);
        console.log(jQuery('input[name="'+jQuery( original ).attr('data-value')+'"]').val());
        return false;
      }
    })
    .data( "ui-autocomplete" )._renderItem = function( ul, item ) {
      return jQuery( "<li>" )
        .append( "<a>" + item.name + "</a>" )
        .appendTo( ul );
    };
  }
  
  function modal_status(data){
     if(data.succes){
                jQuery('#dp-generic h3').html('All is fine');
    // console.log('succes');
      }else{
          jQuery('#dp-generic h3').html('Warning');
   //console.log('problem');
      }
      jQuery('#dp-generic.modal-body').html('');

      for (key in data.msg){
       //   console.log(data.msg[key]);
          jQuery('#dp-generic .modal-body').append('<li>'+data.msg[key]+'</li>');
      }
      open_modal('#dp-generic');
  }
  
  
  /*
  if ((jQuery(this).attr('data-type') == 'needed') && (!jQuery(this).is(':checked'))) {
                    console.log('here2');
                    jQuery(':checkbox[data-id="' + jQuery(this).attr('data-id') + '"]').each(function() {
                        jQuery(this).attr('checked', false);

                    });
                } else if ((jQuery(this).attr('data-type') == 'needed') && (jQuery(this).is(':checked'))) {

                    jQuery(':checkbox[data-id="' + jQuery(this).attr('data-id') + '"]').each(function() {
                        jQuery(this).attr('checked', true);

                    });
                } else {
                    console.log('here3');
                    jQuery(':checkbox[data-id="' + jQuery(this).attr('data-id') + '"]').each(function() {
                        if (jQuery(this).attr('data-type') == 'needed') {
                            jQuery(this).attr('checked', true);
                        }
                    });
                }*/
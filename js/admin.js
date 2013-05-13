jQuery(document).ready(
        function() {
            jQuery("#tabs").tabs();
        });
var i = 0;
var total = 0;
function DP_search_sparql() {

    query = 'query=' + escape(jQuery('textarea').val()) + '&output=json';
    jQuery.ajax({
        url: jQuery('#url').val(),
        type: 'get',
        data: query,
        dataType: 'json'
    }).done(function(data) {
        console.log(data.results.bindings);
        out1 = "";
        out2 = "";
        if (data.results.bindings != null || data.results.bindings.length > 0) {
            jQuery("#dp_user").append('<input type="button" onclick="DP_insert_all(true);" value="Insert user in wordpress"><br/>');
            jQuery("#dp_user").append('<progress id="prog" value="0" max="' + data.results.bindings.length + '"></progress>');

            for (result in data.results.bindings) {
                out1 = "";
                out2 = "";
                j = 0;
                for (key in data.results.bindings[result]) {
                    if (data.results.bindings[result][key].type == 'literal') {
                        out1 = '<ul><li>' + data.results.bindings[result][key].value + '</li><input type="hidden" name="value[' + i + '][user]" value="' + data.results.bindings[result][key].value + '" />';
                    } else {
                        out2 += '<li>' + data.results.bindings[result][key].value + '</li><input type="hidden" name="value[' + i + '][uri][' + j + ']" value="' + data.results.bindings[result][key].value + '" />';
                        j++;
                    }
                }
                i++;
                jQuery("#dp_user").append(out1 + '<ul>' + out2 + '</ul>');
            }
        }
    });




}

function DP_insert_all(all) {
    var algo = jQuery('#dp_user').serializeObject();



    for (var key in algo.value) {
        DP_insert_user(algo.value[key]);
    }


}

function DP_insert_user(name) {

    var data = {
        action: 'add_user',
        value: name
    };
    jQuery.ajax({
        url: "admin-ajax.php",
        type: 'post',
        data: data,
        dataType: 'json'
    }).done(function(data) {
        jQuery('#prog').attr('value', ++total);

    });

}

function DP_uri_user() {

    var data = {
        action: 'add_uri',
        value: jQuery("#dp-uri").serializeObject()
    };
    jQuery.ajax({
        url: "admin-ajax.php",
        type: 'post',
        data: data,
        dataType: 'json'
    }).done(function(data) {
        jQuery('#prog').attr('value', ++total);

    });

}
function DP_search_user() {

    var data = {
        action: 'search_user',
        value: jQuery('#dp-user').val()
    };
    jQuery.ajax({
        url: "admin-ajax.php",
        type: 'post',
        data: data,
        dataType: 'html'
    }).done(function(data) {
        
        jQuery('#dp-search-user').val('');
        jQuery('#dp-search-user').append(data);
        DP_add_html_uri();
    });
    
}

function DP_add_html_uri(){
     jQuery('#dp-add-uri').show();
     jQuery('#dp-add-uri').append('<br/>URI<input type="text" name="uri[]"/>');    

}



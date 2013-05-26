jQuery(document).ready(
        function() {

            jQuery(document).on('click', '#dp-save-relation-user-source', function() {
                dp_save_relation_user_source(this);
            });
            
             jQuery(document).on('click', '#dp-delete-relation-user-source', function() {
                dp_delete_relation_user_source();
            });
            jQuery(document).on('click', '#dp-update-relation-user-source', function() {
                dp_update_relation_user_source();
            });
            jQuery(document).on('click', '#dp-save-sparql', function() {
                dp_save_source();
            });
        });
function dp_save_source() {
    var name = 'add_source';
    var data = {
        action: name,
        value: jQuery('#dp-add-source').serializeObject()
    };
    var result = DP_ajax_server(data);
    result.done(function(data) {
        if (data.succes) {
            jQuery("#dp-save-sparql").hide();
            jQuery("#dp-verify-sparql .modal-body").html("<p><b>Saved!!</b></p>");
        }
    });

}
function dp_show_source() {

    var uri_sparql = jQuery('#uri_sparql').val();
    var uri_base = jQuery('#uri_base').val();
    var squery = jQuery('#sparql').val();
    squery = squery.replace(/uri_base/gi, uri_base);
    var body = DP_search_sparql2(squery, uri_sparql);
    body.done(function(data) {
        if (data.results.bindings.length > 0) {
            console.log(data.results.bindings[0]);
            jQuery('#name_uri_base').val(data.results.bindings[0].conferenceName.value);
            jQuery("#dp-verify-sparql .modal-body").html("<p><b>The name of the conference is:</b></p><h4>" + data.results.bindings[0].conferenceName.value + "</h4>");
            jQuery("#dp-save-sparql").show();

        } else {

            jQuery("#dp-verify-sparql .modal-body").html("<p><b>Sorry nothing to show, try again</b></p><h4>" + data.results.bindings[0].conferenceName.value + "</h4>");
        }
    });
    open_modal('#dp-verify-sparql');
    jQuery("#dp-save-sparql").hide();
 
}



function dp_save_relation_user_source(obj) {

    var name = 'add_relation_user_source';
    var data = {
        action: name,
        value: jQuery('#dp-add-relation-user-source').serializeObject()
    };

    var reponse = DP_ajax_server(data);
    reponse.done(function(data) {
       if(data.succes){
            document.location.reload(true);
        }

    });
}




function dp_delete_relation_user_source() {
    var name='delete_relation_user_source';
    var data = {
        action: name,
        value: jQuery('#dp-delete-relation-user-source-fom').serializeObject()
    };
    var result = DP_ajax_server(data);
    result.done(function(data) {
        if(data.succes){
              document.location.reload(true);
        }
      
    });
}
function dp_update_relation_user_source() {
    var data = {
        action: name,
        value: jQuery('#dp-update-relation-user-source-form').serializeObject()
    };
    var result = DP_ajax_server(data);
    result.done(function(data) {
        if(data.succes){
            document.location.reload(true);
        }
    });
}

var i = 0;
var total = 0;

var mail_sha1;

jQuery(document).ready(
        function() {
    /*
            jQuery(document).on('click', '.search-list-users-uri', function() {
                insert_type_entities(this);
            });*/
            jQuery(document).on('change', '#search-all-user-sparql .dp-list-source', function() {
                search_all_user(this);
            });
            
            jQuery(document).on('change', '#dp-user-list', function() {
                show_camps('#dp-creation-user-mail');
            });
            
             jQuery(document).on('click', '#dp-check-input', function() {
              check_mail();
            });
            jQuery(document).on('click', '#dp-creation-modal-continue', function() {
              add_user();
            });
            jQuery(document).on('change', '#dp-list-source-uri', function() {
              jQuery('#dp-type-entities').show();
            });
            jQuery(document).on('change','#dp-type-entities .dp-type-entity',function(){
                search_entities();
                
            });
            jQuery(document).on('change', '#dp-list-entities .dp-list-entity', function() {
              jQuery('#dp-add-uri').show();
            });
            
            jQuery(document).on('click','#dp-uri-user',function(){
                add_uri_user();
            });
        });
/*
function insert_type_entities(obj) {
    jQuery('#type-entities').remove();
    jQuery('#type-entities').append('<select class=""><option value="1">>Author</option<option value="2">Uri</option></select>');
}
*/
function search_all_user(obj) {
    var sparql = jQuery('textarea[name="dp-global-sparql"]').val();
    //  console.log(sparql);
    var url_base = jQuery('input[data-source="' + jQuery(obj).attr('data-source') + '"],[name="uri_base"]').val();
    //   console.log(url_base);
    var squery = sparql.replace(/uri_base/g, url_base);
    //   console.log(squery);
    var uri_sparql = jQuery('input[data-source="' + jQuery(obj).attr('data-source') + '"],[name="uri_sparql"]').val();
    //   console.log(uri_sparql);
    var users = DP_search_sparql2(squery, uri_sparql);
    
    users.done(function(data) {
        var json = jQuery.parseJSON(jQuery('#dp-list-user-wp').attr('data-user'));
        //     console.log(data);

        if (data.results.bindings.length > 0) {
            var search = [];
            var json = jQuery.parseJSON(jQuery('#dp-list-user-wp').attr('data-user'));
            console.log(json);
            search=put_reponse_in_array(data);
            mail_sha1 = make_list_unique(search,json);
            show_list_unique(mail_sha1);
        }

    });
   
}
function put_reponse_in_array(data){
     var search = [];
         jQuery(data.results.bindings).each(function(index, value) {
                search[value.mailEncryp.value] = value;
            });
            return search;
}

function make_list_unique(vector,json) {
    console.log(json);
    jQuery(json.result).each(function(index, value) {
        if (typeof vector[value.mailEncryp] !== 'undefined') {
            delete vector[value.mailEncryp];
        } else {

        }
    });
    return vector;
}

function show_list_unique(vector) {
    console.log(vector);
    var out='';
    for (result in vector) {
        console.log(vector[result].mailEncryp);
       out+='<option value="'+vector[result].mailEncryp.value+'">'+vector[result].name.value+ '</option>';
    }
    jQuery('#dp-creation-user select').html('');
    jQuery('#dp-creation-user select').append(out);
}
function show_camps(id) {
    jQuery(id).show();
}

function check_mail(){
    console.log('Check mail');
    var mail=jQuery('#dp-mail').val();
    var atpos=mail.indexOf("@");
var dotpos=mail.lastIndexOf(".");
if (atpos<1 || dotpos<atpos+2 || dotpos+2>=mail.length)
  {
  open_modal('#mail-false');
  return;
  }
    console.log(mail);
    var mail_encryp=SHA1(mail);
    console.log('mail_encryp');
    console.log(mail_encryp);
    var value_select_sha1=jQuery('#dp-user-list :selected').val();
    console.log('value_select_sha1');
    console.log(value_select_sha1);
    if(mail_encryp!=value_select_sha1){
        console.log('is not the same');
        open_modal('#dp-creation-modal');
    }else{
        console.log('trying to add');
        add_user();
    }
}

function open_modal(id) {
  jQuery(id).modal("show");
}

function close_modal(id) {
  $(id).modal("hide");
}
function add_user(){
    var mail=jQuery('#dp-mail').val();
    
    console.log('mail :'+mail);
    var id_source=jQuery('#search-all-user-sparql .dp-list-source :selected').attr('data-source');
    console.log('id_source :'+id_source);
    var name=jQuery('#dp-user-list :selected ').html();
    console.log('name :'+name);
      var data = {
        action: 'add_user',
        value: {user_mail:mail,id_source:id_source,name:name}
    };
    console.log('data');
    console.log(data);
    
    var user=DP_ajax_server(data);
    user.done(function(data){
        modal_status(data);
    });
}
   
function search_entities(){
    console.log('value of type');
    var type=jQuery('#dp-type-entities .dp-type-entity').val();
    console.log(type);
    console.log('data source');
     var id_source=jQuery('#dp-list-source-uri .dp-list-source :selected').attr('data-source');
     console.log(id_source);
     var data = {
        action: 'search_entities',
        value: {'type':type,'id_source':id_source}
    };
     var entities=DP_ajax_server(data);
     entities.done(function(data){
         if(data.succes){
             console.log(data.result);
             var out="";
             if(data.result.results.bindings.length>0){
             for (value in data.result.results.bindings){
                 out+='<option data-uri="'+escape(data.result.results.bindings[value].uri.value)+'" >'+data.result.results.bindings[value].name.value+'</option>';
             }
             jQuery('#dp-list-entities .dp-list-entity').html('');
             jQuery('#dp-list-entities .dp-list-entity').append(out);
             jQuery('#dp-list-entities').show();
             }
          
         }else{
              var data={succes:false,msg:[{0:'Problem with request sparql'}]};
              modal_status(data); 
              
            
         }
     });
}


function add_uri_user(){
    
    var uri=unescape(jQuery('#dp-list-entities .dp-list-entity :selected').attr('data-uri'));
    var name=jQuery('#dp-list-entities .dp-list-entity').val();
    console.log(uri);
    console.log('data source');
    var id_user=jQuery('#dp-search-user input[name="id_user"]').val();
    console.log(id_user);
    var data = {
        action: 'add_uri_user',
        value: {id_user:id_user,uri:uri,name:name}
    };
     var entities=DP_ajax_server(data);
     entities.done(function(data){
         if(data.succes){
           modal_status(data); 
         }
     });
}
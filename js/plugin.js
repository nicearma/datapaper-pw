var i = 0;
var id;
jQuery(document).ready(
        function() {
            jQuery(document).on('focus', 'input.special', function() {

                jQuery(this).popover('show');
            });
            jQuery(document).on('click', '#key input[type="button"]', function() {
                DP_searchValeu(this);
            });
            jQuery(document).on('change', '.dp-url', function() {
                DP_verifyMime(this);
            });
        });
function DP_searchValeu(obj) {

    id = jQuery(obj).attr('data-value');

    if (id != "") {
        jQuery.ajax({
            url: "http://dataconf.liris.cnrs.fr:5984/datapaper/_design/public/_view/by_all2",
            data: 'key="' + id + '"',
            crossDomain: true,
            dataType: 'jsonp'
        }).done(function(data) {
            if (data.rows[0] == null) {
                DP_addTable(null, true);
                i = 0;
            } else {
                DP_addTable(data.rows[0].value, true);

            }
        });
    }
}


function DP_verifyMime(obj) {
    var options;
    var value = jQuery(obj).val();
    //validation simple of mail
    var regex = /@/ig;
    //validaton of phone
    var regex2 = /^\+?[0-9+]/ig;
    var regex3 = /\.(jpg|png|gif|jpeg)\/?$/ig;
    var regex4 = /\.(php|html|htm|asp|jps)\/?$/ig;
    //For the mail
    if (regex.test(value)) {
        regex = /mailto/ig;
        if (!regex.test(value)) {
            jQuery(obj).val('mailto:' + jQuery(obj).val());
        }
        options = jQuery('#dp-type-contact');
        //validation of phone
    } else if (regex2.test(value)) {
        regex = /phone/ig;
        if (!regex.test(value)) {
            jQuery(this).val('phone:' + jQuery(this).val());
        }
        options = jQuery('#dp-type-contact');
        //validation of image
    } else if (regex3.test(value)) {
        options = jQuery('#dp-type-image');
        //validation for web-other
    } else if (regex4.test(value)) {
        options = jQuery('#dp-type-other');
    } else {

    }
    var parent = jQuery('#datapaperForm table');
    console.log("mon num" + jQuery(obj).data('num'));
    jQuery(options).attr('name', 'public[' + jQuery(obj).data('num') + '][type]');

    //special erasable
    jQuery(parent).find('td[data-num="' + jQuery(obj).data('num') + '"]').parent().show();
    jQuery(parent).find('td[data-num="' + jQuery(obj).data('num') + '"]').append(options);
}

function DP_addTable(value, editable) {
    DP_cancel();
    jQuery('#datapaper_search').hide();
    jQuery('#datapaper_command').show();
    var out = '<form id="datapaperForm"><table class="table"><thead><tr><th>Name</th><th>Value</th></tr></thead><tbody id="data">';
    out += "</tbody></table>";
    jQuery("#datapaper_info").append(out);
    out = '<tr><div id="basic"><td>id</td><td id="_id">' + id + '<input type="hidden" name="_id" value="' + id + '" /></td></tr>';
    if (value !== null) {
        out += '<tr><td>Revision</td><td id="_rev">' + value._rev + '<input type="hidden" name="_rev" value="' + value._rev + '" /></td></tr></div>';
    }
    jQuery("#data").append(out);

    DP_addMoreInfo(value, editable);
    jQuery("#data").append('</form>');
}
function DP_addMoreInfo(value, editable) {
    var out = '';
    var table = jQuery('#datapaper_special').clone();
    var help;
    if (value !== null) {

        for (var j = 0; j < value.public.length; j++) {

            for (key in value.public[j]) {
                if (key == "type") {

                } else if (key == "url") {

                } else {
                    help = jQuery(table).find(".dp-" + key);
                    jQuery(help).attr('data-num', j);
                    jQuery(help).attr('name', 'public[' + j + '][' + key + ']');
                    jQuery(help).val(value.public[j][key]);
                }
            }
            console.log(jQuery(table).find('tbody').html());
            out += jQuery(table).find('tbody').html();
        }
        i = value.public.length;
        for (var key in value.private.edit_by) {
            out += '<tr><td>' + key + '</td><td>' + value.private.edit_by[key] + '</td></tr>';
        }
    } else {
        jQuery(table).find(".dp-url").attr('name', 'public[' + i + '][url]');
        jQuery(table).find(".dp-url").attr('data-num', i);
        jQuery(table).find(".dp-description").attr('name', 'public[' + i + '][description]');
        jQuery(table).find(".dp-description").attr('data-num', i);
        jQuery(table).find(".dp-type").attr('data-num', i);
        out += jQuery(table).find('tbody').html();
        i++;
    }
    jQuery("#data").append(out);

}

function DP_sendValue() {

    var data = {
        action: 'send_info',
        value: jQuery("#datapaperForm").serializeObject()
    };
    //  console.log(jQuery("#datapaperForm").serializeObject());
    jQuery.ajax({
        url: "admin-ajax.php",
        type: 'post',
        data: data,
        dataType: 'json'
    }).done(function(data) {
        var data2;
        if (data.hasOwnProperty('rev')) {
            jQuery('#_rev').val(data.rev);
            jQuery('input [name="_rev"]').val(data.rev);
            data2 = {succes: true, msg: ['Update!!']};

        } else if (data.hasOwnProperty('error')) {
            data2 = {succes: true, msg: ['Sorry but cant update the value!!']};
        }
        modal_status(data2);
        DP_cancel();
    });
}


function DP_cancel() {
    jQuery("#datapaper_info").html("");
    jQuery('#datapaper_command').hide();
    jQuery('#datapaper_search').show();
}



                var i = 0;
                var id;

                function DP_searchValeu() {

                    id = jQuery("#key option:selected").val();

                    if (id != "") {
                        jQuery.ajax({
                            // url: "http://dataconf.liris.cnrs.fr:5984/datapaper/_design/public/_view/by_all",
                            url: "http://dataconf.liris.cnrs.fr:5984/datapaper/_design/public/_view/by_all2",
                            data: 'key=["' + id + '"]',
                            crossDomain: true,
                            dataType: 'jsonp'
                        }).done(function(data) {
                            if (data.rows[0] == null) {
                                DP_addTable(null, true);
                            } else {
                                DP_addTable(data.rows[0].value, true);
                                i = 0;
                            }

                        });
                    }


                }
                function DP_addTable(value, editable) {
                    DP_cancel();
                    jQuery('#datapaper_search').hide();
                    jQuery('#datapaper_command').show();
                    var out = '<form id="datapaperForm"> <table><thead><tr><th>Name</th><th>Value</th></tr></thead><tbody id="data">';
                    out += "</tbody></table>";
                    jQuery("#datapaper_info").append(out);
                    out = '<tr><div id="basic"><td>id</td><td id="_id">' + id + '<input type="hidden" name="_id" value="' + id + '" /><img src="'+urlPath+'image/add.png" onclick="DP_addMoreInfo(null,true)"/>"</td></tr>';
                    if (value !== null) {
                        out += '<tr><td>Revision</td><td id="_rev">' + value._rev + '<input type="hidden" name="_rev" value="' + value._rev + '" /></td></tr></div>';
                    }
                    jQuery("#data").append(out);

                    DP_addMoreInfo(value, editable);


                }

                function DP_addMoreInfo(value, editable) {
                    var out = '';
                    if (value !== null) {
                        // alert(value.public.length);
                        for (var j = 0; j < value.public.length; j++) {

                            for (key in value.public[i]) {
                                out += '<tr><td>' + key + '</td><td>';
                                if (editable) {
                                    out += '<input type="text" name="public[' + j + '][' + key + ']" value="' + value.public[j][key] + '"/>';
                                } else {
                                    out += value.public[j][key];
                                }
                                out += ' </td></tr>';

                            }
                        }
                        i = value.public.length;
                        for (var key in value.private.edit_by) {
                            out += '<tr><td>' + key + '</td><td>' + value.private.edit_by[key] + '</td></tr>';
                        }
                    } else {
                        out += '<tr>';
                        out += '<td>Url</td><td><input type="text" name="public[' + i + '][url]"/></td><tr><td>Description</td><td><input type="text" name="public[' + i + '][description]"/></td><tr><td>Type</td><td><input type="text" name="public[' + i + '][type]"/></td>';
                        out += '</tr>';
                        i++;
                    }
                    jQuery("#data").append(out);
                    jQuery("#data").append('</form>');
                }


                function DP_sendValue() {

                    var data = {
                        action: 'send_info',
                        value: jQuery("#datapaperForm").serializeObject()
                    };
                    console.log(jQuery("#datapaperForm").serializeObject());
                    jQuery.ajax({
                        url: "admin-ajax.php",
                        type: 'post',
                        data: data,
                        dataType: 'json'
                    }).done(function(data) {

                        console.log(data);
                        DP_cancel();
                    });
                }
  function DP_DeleteValue() {

                    var data = {
                        action: 'delete_info',
                        value: jQuery("#datapaperForm").serializeObject()
                    };
                    console.log(jQuery("#datapaperForm").serializeObject());
                    jQuery.ajax({
                        url: "admin-ajax.php",
                        type: 'post',
                        data: data,
                        dataType: 'json'
                    }).done(function(data) {

                        console.log(data);
                        DP_cancel();
                    });
                }


                function DP_cancel() {
                    jQuery("#datapaper_info").html("");
                    jQuery('#datapaper_command').hide();
                    jQuery('#datapaper_search').show();
                }



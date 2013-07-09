<?php
function insert_modal($id = 'dp-generic') {
    
    ?>
    <div id="<?php echo $id; ?>" class="modal hide fade">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h3></h3>
                </div>
                <div class="modal-body">
                    <p> </p>
                </div>
                <div class="modal-footer">
                    <a href="#" data-dismiss="modal" class="btn">Close</a>
                </div>
            </div>
    <?php
}

function search_user() {
    ?>
    <input type="text" class="auto-search" data-value="id_user" <?php echo 'data-object="' . htmlentities(json_encode(DP_SQL::DP_get_user())) . '"'; ?> />
    <input type="hidden" name="id_user" />
    <?php
}

function search_source() {
    ?>

    <input type="text" data-value="id_source" class="auto-search" <?php echo 'data-object="' . htmlentities(json_encode(DP_SQL::DP_get_source_auto())) . '"'; ?>>
    <input type="hidden" name="id_source">
    <?php
}

function search_source_select() {
    $result = DP_SQL::DP_get_source_auto();

    if (count($result) > 0) {
        echo '<select id="id_source" name="id_source"><option value="">Please select one datasource</option>';
        foreach ($result as $value) {
            echo '<option value="' . $value->id . '">' . htmlentities($value->name) . '</option>';
        }
        echo '</select>';
        ?>
        <?php
    } else {
        ?>
        <h3>Please contact with the admin of the plugin, because you can chose any data source</h3>
        <?php
    }
}

function list_entities() {
    ?>

    <input type="text" data-value="id_entities" class="auto-select" <?php echo 'data-object="' . htmlentities(json_encode(Configuration::get_sparql_entities($mail))) . '"'; ?>>
    <input type="hidden" name="id_source">
    <?php
}

function html_list_source_by_user() {


    global $current_user;
    get_currentuserinfo();
    $sources = DP_SQL::DP_get_source_by_user($current_user->id);
    //var_dump($sources);
    if (count($sources)) {
        $out1 = '<p><b>List of source :</b></p>
                    <select class="dp-list-source">
                    <option>Please select one</option>';
        // var_dump($sources);
        foreach ($sources as $source) {
            //  var_dump($source);
            echo '<input type="hidden" data-source="' . $source->id_source . '" name="uri_sparql" value="' . $source->uri_sparql . '" />';
            echo '<input type="hidden" data-source="' . $source->id_source . '" name="uri_base"  value="' . htmlentities($source->uri_base) . '" />';
            $out1.= '<option data-source="' . $source->id_source . '" class="search-all-user-sparql" value="' . htmlentities($source->name_uri_base) . '">' . $source->name_uri_base . '</option>';
        }

        echo $out1 . '</select>';

        echo '<input id="dp-list-user-wp" name="blabla" type="hidden" data-user="' . htmlentities(json_encode(['result' => DP_SQL::DP_get_user()])) . '" />';
        echo '<textarea style="visibility:hidden;" name="dp-global-sparql" >' . Configuration::get_sparql_entities_author() . '</textarea>';
    } else {
        echo '<h3>You dont have any source</h3>';
    }
}
?>

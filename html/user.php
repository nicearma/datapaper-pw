<?php
add_action('admin_init', 'datapaper_plugin_init');

function datapaper_plugin_init() {
    add_action('wp_ajax_send_info', 'send_info_callback');
    add_action('wp_ajax_delete_info', 'delete_info_callback');
    wp_enqueue_script('dp-plugin-script');
}

function datapaper_user() {
    echo '<script type="text/javascript">var urlPath="' . plugin_dir_url(__FILE__) . '../img/";</script>'
    ?>

    <div id="datapaper">
    <?php
    insert_modal();
    ?>
        <h1>Add external resource</h1>

        <p>You are responsible for the following entities:</p>

        <div id="datapaper_search">
    <?php
    global $current_user;
    get_currentuserinfo();
    $result = DP_SQL::DP_get_URI($current_user->ID);
    if (count($result) > 0) {
        echo '<div id="key">';
        foreach ($result as $key => $value) {
            echo '<input type="button" class="btn btn-primary" data-value="' . $value->uri . '" value="' . $value->name . '" /><br/><br/>';
        };
        echo '</div>';
    } else {
        echo '<h3>You dont have any entity to add resources to.</h3>';
    }
    ?>

        </div>
        <div id="datapaper_info">

        </div>

        <div id="datapaper_command" class="buttoncontainer" hidden="">
    <?php echo '<img src="' . plugin_dir_url(__FILE__) . '../img/add.png" onclick="DP_addMoreInfo(null,true)" />'; ?>
            <button type="button" id="datapaper_send" class="button button-primary" onclick="DP_sendValue()">Send</button>
            <!--<button type="button" id="datapaper_delete" onclick="DP_DeleteValue()" class="button button-secondary">Delete</button>-->
            <button type="button" id="datapaper_cancel" class="button button-secondary" onclick="DP_cancel()">Cancel</button>
        </div>  

        <table id="datapaper_special" hidden>
                <tr>
                    <td>Description</td>
                    <td><textarea  class="erasable dp-description" name="" ></textarea></td>
                </tr> 

                <tr>
                    <td>URI</td>
                    <td><input type="text" class="erasable dp-url" name="" value=""/></td>
                </tr> 
                <tr hidden> 
                    <td>Type</td> 
                    <td class="dp-type"></td>
                </tr> 
        </table>
        <div id="dp-type-help"  hidden>
        <select id="dp-type-other">
            <option selected>Select one</option>
            <option value="hasWebPage">WebPage</option>
            <option value="hasOther" >Other Type</option>
        </select>
        <select id="dp-type-image">
            <option selected>Select one</option>
            <option value="hasValue">Photo</option>
             <option value="hasValue" >Avatar</option>
        </select>
        <select id="dp-type-contact">
             <option value="hasContact" selected>Contact</option>
        </select>

    </div>

    <?php
}
/**
 * Send the information to couchDB
 */
function send_info_callback() {
    $info = new Info();
    $info->makeConnection();
    $info->setInfo($_POST['value']);
    $info->addPrivate();
    echo json_encode($info->sendInfo());
    die();
}
/**
 * Please be caraful this function have problem you can delete all the database couchDB
 */
function delete_info_callback() {
    $info = new Info();
    $info->makeConnection();
    $info->setInfo($_POST['value']);
    $info->addPrivate();
    //  $info->deleteInfo();
    die();
}
?>

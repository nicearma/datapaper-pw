<?php
/**
 * All for the form register in wordpress
 */
add_action('register_form', 'dp_register_form');
add_action('wp_ajax_nopriv_verify_mail', 'verify_mail_callback');

/**
 * Call for validate the mail in the post form
 */

function verify_mail_callback() {
    $value = $_POST['value'];
    $out = ['succes' => false];
    //we verify if he chose an source in the form
    if (!empty($value['id_source'])) {
        $id_source = $value['id_source'];
        //if the id is numeric (security)
        if (is_numeric($id_source)) {
            //if the email is really un email
            if (filter_var($value['user_email'], FILTER_VALIDATE_EMAIL)) {
                //create un object for the automatic insert of entities
                $auto = new Auto($id_source);
                $auto->set_mail($value['user_email']);
                //if the email exist
                if ($auto->verify_mail()) {
                    $data = [];
                    //get all the entities author with the email
                    $auto->get_entities_author();
                    //add this information to array
                    foreach ($auto->get_results() as $value) {
                        $data = array_merge($data, ['name' => $value->name->value, 'uri' => $value->uri->value]);
                    }
                    //find all the entities publication
                    $auto->get_entities_publication();
                    //add this information to array
                    foreach ($auto->get_results() as $value) {
                        $data = array_merge($data, ['name' => $value->name->value, 'uri' => $value->uri->value]);
                    }
                    $out['succes'] = true;
                    $out = array_merge($out, $data);
                }
            }
        }
    }
    echo json_encode($out);
}
/**
 * Add in the form a new element
 */
function dp_register_form() {
    wp_enqueue_script('dp-form-registration');
    echo '<p><label>Please select the dataset</label></p>';
    //add <option> (help.php)
    search_source_select();
    wp_enqueue_script('dp-bootstratp');
    wp_enqueue_style('dp-bootstratp-css');
}

add_filter('registration_errors', 'dp_registration_errors', 10, 3);
/**
 * Verify if all the information in the post is good
 * @param type $errors
 * @param type $sanitized_user_login
 * @param type $user_email
 * @return type
 */

function dp_registration_errors($errors, $sanitized_user_login, $user_email) {
    if (empty($_POST['id_source'])) {
        $errors->add('id_source_error', __('<strong>ERROR</strong>: The source can\'t be null.', 'mydomain'));
    } else {
        $id_source = $_POST['id_source'];

        if (!is_numeric($id_source)) {
            $errors->add('id_source_error', __('<strong>ERROR</strong>: The source cant be have null.', 'mydomain'));
        } else {
            if (!filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)) {
                $errors->add('user_mail_error', __('<strong>ERROR</strong>: The mail is not good.', 'mydomain'));
            } else {
                $auto = new Auto($id_source, $_POST['user_email']);

                if (!$auto->verify_mail()) {
                    $errors->add('user_email_error', __('<strong>ERROR</strong>:Sorry but we cant find any relation between the source and your mail, please contact to the admin for more.', 'mydomain'));
                }
            }
        }
    }

    return $errors;
}

add_action('user_register', 'dp_insertion_entities_form');
/**
 * 
 * @param type $user_id
 */
function dp_insertion_entities_form($user_id) {

    if (!empty($_POST['value'])) {
        $id_source = $_POST['value']['id_source'];
        $user_email = $_POST['value']['user_mail'];
 
    } else {
        $id_source = $_POST['id_source'];
        $user_email = $_POST['user_email'];
    }


    if (!empty($user_email) && !empty($id_source)) {
        dp_insertion_of_entities($id_source, $user_email, $user_id);
    }
}
/**
 * 
 * @param type $id_source
 * @param type $user_email
 * @param type $user_id
 */
function dp_insertion_of_entities($id_source, $user_email, $user_id) {

    $auto = new Auto($id_source, $user_email);
    $auto->get_entities_author();
    $auto->insert_entities($user_id);
    
    //Find the user with the id
    $userLogin;
    //insert les données de gravatar
    $auto->insert_gravatar($email, $userLogin);
    $auto->get_entities_publication();
    $auto->insert_entities($user_id);
}

?>

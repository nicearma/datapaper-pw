<?php

add_action('register_form', 'dp_register_form');
add_action('wp_ajax_nopriv_verify_mail', 'verify_mail_callback');

function verify_mail_callback() {
    $value=$_POST['value'];
    $out = ['succes' => false];
    if (!empty($value['id_source'])) {
        $id_source =$value['id_source'];

        if (is_numeric($id_source)) {
            if (filter_var($value['user_email'], FILTER_VALIDATE_EMAIL)) {
                $auto = new Auto($id_source);
                $auto->set_mail($value['user_email']);
                if ($auto->verify_mail()) {
                    $data = [];
                    $auto->get_entities_author();
                    
                    foreach ($auto->get_results() as $value) {
                        $data = array_merge($data,['name' => $value->name->value, 'uri' => $value->uri->value]);

                    }
                    $auto->get_entities_publication();
                    foreach ($auto->get_results() as $value) {
                        $data = array_merge($data,['name' => $value->name->value, 'uri' => $value->uri->value]);

                    }
                    $out['succes'] = true;
                    $out = array_merge($out, $data);
                }
            }
        }
    }
    echo json_encode($out);
}

function dp_register_form() {
    wp_enqueue_script('dp-form-registration');
    echo '<p><label>Please select the dataset</label></p>';
    search_source_select();
    wp_enqueue_script('dp-bootstratp');
    wp_enqueue_style('dp-bootstratp-css');
}

add_filter('registration_errors', 'dp_registration_errors', 10, 3);

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
                $auto = new Auto($id_source);
                $auto->set_mail($_POST['user_email']);
                if (!$auto->verify_mail()) {
                    $errors->add('user_email_error', __('<strong>ERROR</strong>:Sorry but we cant find any relation between the source and your mail, please contact to the admin for more.', 'mydomain'));
                }
            }
        }
    }

    return $errors;
}

add_action('user_register', 'dp_insertion_entities_form');

function dp_insertion_entities_form($user_id) {
   $id_source = $_POST['id_source'];
   $mail=$_POST['user_email'];
   
   dp_insertion_entities($id_source, $mail);
}

function dp_insertion_entities($id_source ,$mail,$user_id=null){
    if(empty($user_id)){
       $user = get_user_by('email', $mail );
        if(!$user){
        $user_id=$user->ID;
    }else{
         $message=  new WP_Error('datapaper', __("User not valid"));
    }
    }
    $auto = new Auto($id_source,$mail);
    $auto->get_entities_author();
    $auto->insert_entities($user_id);
   $auto->get_entities_publication();
   $auto->insert_entities($user_id);
}
 

?>

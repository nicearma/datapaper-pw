<?php
//if (is_author()) {

add_action('admin_init', 'datapaper_creator_menu');
add_action('wp_ajax_add_user', 'add_user_callback');
//add_action('wp_ajax_search_user', 'search_user_callback');
add_action('wp_ajax_add_uri_user', 'add_uri_user_callback');

add_action('wp_ajax_search_entities', 'search_entities_callback');
add_action('wp_ajax_add_uri_from_post', 'add_uri_from_post_callback');

//}

function datapaper_creator_menu() {

    wp_enqueue_script('dp-creator-script');
}

function datapaper_creator() {
    ?>
    <h1>DATAPAPER CHAIR</h1>

    <div id="tabs">
        <ul>
            <li><a href="#tabs-1">Add user from source</a></li>
            <li><a href="#tabs-2">Add uri to user</a></li>
        </ul>
        <div id="tabs-1">
            <h3>Add user from source</h3>
            <p>You can add users from the source that the admin put in the database</p>
            <p>Please if you have any question contact with the administrator</p>
            <p>This requestis made from database</p>
            <p><b>The uri data source</b></p>
            <div id="search-all-user-sparql">
            <?php
            html_list_source_by_user()
            ?>
            </div>
            <div id="dp-creation-modal" class="modal hide fade">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h3> Warning email dont match</h3>
                </div>
                <div class="modal-body">
                    <p>We make a matching with the mail of the user and we found that is not the same</p>
                </div>
                <div class="modal-footer">
                    <a href="#" data-dismiss="modal" class="btn">Close</a>
                    <a href="#" data-dismiss="modal" class="btn btn-primary">Change email</a>
                    <a href="#" data-dismiss="modal" id="dp-creation-modal-continue" class="btn btn-warning">Continue any way</a>
                </div>
            </div>
            <div id="mail-false" class="modal hide fade">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h3>Warning</h3>
                </div>
                <div class="modal-body">
                    <p>The email is not good </p>
                </div>
                <div class="modal-footer">
                    <a href="#" data-dismiss="modal" class="btn">Close</a>
                </div>
            </div>

            <div id="dp-creation-modal-good" class="modal hide fade">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h3>Account make</h3>
                </div>
                <div class="modal-body">
                    <p>We make a account for this mail</p>
                </div>
                <div class="modal-footer">
                    <a href="#" data-dismiss="modal">Close</a>
                </div>
            </div>
            <form id="dp-creation-user">
                <div  data-pas="0">
                    <p>List of user without a acount</p><br/>
                    <select id="dp-user-list" class="dp-creation-user">
                    </select>
                    <div hidden id="dp-creation-user-mail"><p>Insert the mail</p>
                        <input id="dp-mail" type="email" />
                        <input type="button" id="dp-check-input" value="Insert user" />
                    </div>
                </div>
            </form>
            </div>
            <div id="tabs-2">
                <h3>Here you can add some uri to the user</h3>
                <form id="dp-uri">
                    <div id="dp-search-user">
                        <p><b>Search the user</b></p>
                        
                        <?php search_user(); ?>
                    </div>
                    <div id="dp-list-source-uri">
                       <?php
                            html_list_source_by_user();
                            ?>
                    </div>
                    <div hidden id="dp-type-entities">
                        <p><b>Type of entity</b></p>
                        <select class="dp-type-entity">
                            <option>
                               Chose one
                            </option>
                            <option value="author">
                                Author
                            </option>
                             <option value="publication">
                                Publication
                            </option>
                        </select>
                    </div>
                    <div hidden id="dp-list-entities">
                        <p><b>Chose the entity</b></p>
                       <select class="dp-list-entity">
                            
                       </select>
                    </div>
                          
                    <div hidden id="dp-add-uri">
                        <input type="button" id="dp-uri-user" value="ADD RELATION"/>
                    </div>
                </form>
            </div>
        
        <?php
    }

    function add_user_callback() {
        $out=['succes'=>false];
        $array = $_POST['value'];
        foreach ($array as $value) {
           
            if (empty($value['user_maill'])) {
                $mail = $value['user_mail'];
                $user_id = DP_SQL::DP_add_user($value['name'], $mail);
                if(is_wp_error($user_id)){
                   $out['message'][]=$user_id->get_error_message();
                }else {
                    $out['message'][]='User successfully added';
                    if(!empty($value['id_source'])){
                    $out['message'][]="User create";
                    $id_source=$value['id_source'];
                   
                    dp_insertion_entities($id_source, $mail,$user_id);
                    $out['message'][]="All fine";
                    $out['succes']=true;
                }else{
                     $out['message'][]='Sorry but we cant not add entities without the id of source';
                }
                }
                
            }   
        }
        echo json_encode($out);
        die();
    }
    function search_entities_callback() {
        $value=$_POST['value'];

        $out= ['succes'=>false];
        if(!empty($value['type'])&&!(empty($value['id_source']))&&(is_numeric($value['id_source']))){
        $type=$value['type'];
        $id_source=$value['id_source'];
        if($type=="author"){
          $sparql=  Configuration::get_sparql_entities_author();
        }else if($type="publication"){
           $sparql= Configuration::get_sparql_entities_publication();
        }
        $result=get_sparql($id_source,$sparql, $result);
       $out= ['succes'=>true,'result' => $result];
        }
       echo json_encode($out);
        die();
    }

    function add_uri_user_callback(){
        $value=$_POST['value'];
        if(!empty($value['id_user'])&&!empty($value['uri'])&&!(empty($value['name']))){
        $id_user=$value['id_user'];
        $uri=$value['uri'];  
        $name=$value['name'];    
        $out=add_uri_user($uri, $id_user,$name);
        }else{
            $out=['succes'=>false,'msg'=>'Problem with the form'];
        }
        echo json_encode($out);
        die();
        
    }
    
    function add_uri_user($uri, $id,$name) {
            $array = ['id_user' => $id, 'uri' => $uri,'name'=>$name];
            return DP_SQL::DP_insert_uri_user($array);

    }
    ?>

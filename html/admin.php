<?php
if (is_admin()) {
    add_action('admin_init', 'datapaper_admin_menu');
    add_action('wp_ajax_search_source', 'search_source_callback');
    add_action('wp_ajax_search_user', 'search_user_callback');
    add_action('wp_ajax_add_uri', 'add_uri_user_callback');
    add_action('wp_ajax_add_source', 'add_source_callback');
    add_action('wp_ajax_update_source', 'update_source_callback');
    add_action('wp_ajax_add_relation_user_source', 'add_relation_user_source_callback');
    add_action('wp_ajax_delete_relation_user_source', 'delete_relation_user_source_callback');
    
}

function datapaper_admin_menu() {
   
    wp_enqueue_script('dp-admin-script');
}

function datapaper_admin() {
   // insert_modal();
//dp_insertion_entities(1);
    
    ?>
<div id="dp-verify-sparql" class="modal hide fade">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h3> Testing the uri source/base</h3>
                </div>
                <div class="modal-body">
                    <p>Waiting for the data, please wait</p>
                </div>
                <div class="modal-footer">
                    <a href="#" data-dismiss="modal" class="btn">Close</a>
                    <a href="#"  id="dp-save-sparql"  class="btn btn-primary">Save in database</a>
                </div>
            </div>
    <h1>DATAPAPER ADMIN</h1>
    <div id="tabs">
        <ul>
             <li><a href="#tabs-1">Add sparql source</a></li>
            <li><a href="#tabs-2">See/Modify/Delete sources</a></li>  
           <li><a href="#tabs-3">Add/Delete creator-source</a></li>
        </ul>
        <div id="tabs-1">
            <h3>Sparql source</h3>
            <p>Here you can put different sparql source</p>
            <p>For the moment this plugin only work with the type of response json and with the one request static, the request for the server
                is going to be </p>
            <pre>
SELECT DISTINCT ?authorName  ?authorUri ?uriPubli ?authorMailEncryp WHERE  { 
            ?uriPubli swc:isPartOf <b>The url base</b>. 
                ?authorUri foaf:made ?uriPubli. 
                ?authorUri foaf:mbox_sha1sum ?authorMailEncryp. 
                ?authorUri foaf:name ?authorName. 
                } ORDER BY ASC(?authorName) 
            </pre>
            <p><b>Ex for uri spaql</b></p>
            <pre>http://posters.www2012.org:8080/sparql/</pre>
            <p>This is one example of the uri base</p>
            <p><b>Ex for the url</b></p>
            <pre>&#60;http://data.semanticweb.org/conference/www/2012/proceedings></pre>

            <form id="dp-add-source">
            <h3>uri sparql</h3>
            <input type="url" name="uri_sparql" id="uri_sparql" />
            <h3>uri base</h3>
            <input type="text" name="uri_base" id="uri_base" />
            <input style="display:none;" name="name_uri_base" id="name_uri_base">
            <textarea id="sparql" style="display:none;">
            <?php  
                    echo Configuration::get_sparql_conference_name();
            ?>
            </textarea>
            <input type="button" class="dp-test" data-action="test_source" value="Test source">
            </form>
        </div>
        <div id="tabs-2">
            <h3>See Sources</h3>
            <p>In this part you can See/Modify/Delete sources</p>
            <div id="dp-source-result">
            <form id="dp-change-source">
                
                <?php
                 $result=   DP_SQL::DP_get_source();
                 /*
                  * Posible implementacion avec autre procedure
                  */
                 if(count($result)>0){
                     $first=true;
                     $out1="";
                     
                     $out3="";

                     foreach ($result as $value) {
                         $out2="";
                         foreach ($value as $key2 => $value2) {
                             if($first){
                                  $out1.='<th>'.htmlentities($key2).'</th>';
                             }
                              $out2.='<td>'.htmlentities($value2).'</td>';
                          }
                           $out3.='<tr>'.$out2.'</tr>';
                          $first=false;
                       }
                       echo '<table><tr>'.$out1.'</tr>'.$out3.'</table>';
                }
                ?>
            
                </form>
                </div>
                
        </div>
         <div id="tabs-3">
            <h3>Add/Delete creator-source</h3>
            <p>In this part you can Add/Delete creator-sources</p>
            <form id="dp-add-relation-user-source">
                    <p><b>Search source</b></p>
                    <p> First step find the user</p>
                    <?php search_user(); ?>
                    <p>Second step find the source</p>
         
                    <?php search_source(); ?>
                    <p>Last step add the relation</p>
                    <input type="button" class="dp-source" value="Add relation source-user" id="dp-save-relation-user-source" >
            </form>
            <form id="dp-delete-relation-user-source-fom">

                 <?php
                   $user_source= DP_SQL::DP_get_source_by_user();

                   if(count($user_source)>0){
                       echo '<input type="button" id="dp-delete-relation-user-source" value="Delete relation source-user" >';
                       $first=true;
                       $out1='<th>Delete</th>';
                       $out2="";
                       
                       foreach ($user_source as $key=>$value ){
                          $out2.='<td><input type="checkbox" name="id[]" value="'. $value->id.'"></td>';
                          foreach ($value as $key2=>$value2 ){
                           if($first){
                            $out1.='<th>'.htmlentities($key2).'</th>';
                           }
                            $out2.='<td>'.htmlentities($value2).'</td>';
                          }
                         $out2= '<tr>'.$out2.'</tr>';
                          $first=false;
                       }
                       echo '<table><tr>'.$out1.'</tr>'.$out2.'</table>';
                   }else{
                       echo 'Sorry but we dont have any relation between user and source';
                   }
                  
                ?>
           
            </form>
        </div>
        
    </div>

    <?php
}

function search_source_callback(){
    if((!empty($_POST['type']))&&($_POST['type']=='all')){
      
       echo json_encode(['result'=>DP_SQL::DP_get_source()]); 
    }else{
        if(!empty($_POST['value'])){
            echo json_encode(['result'=>DP_SQL::DP_get_source($_POST['value'])]);
        }else{
            echo json_encode(['result'=>'']);
        }
      
    }
    die();
}
function update_source_callback(){
    $out=['succes'=>false];
    if(!empty($_POST['value'])){
        $value=$_POST['value'];
        if(!empty($value['uri_sparql'])&&!empty($value['uri_base'])&&!empty($value['name_uri_base'])){
            $result=DP_SQL::DP_update_source($value);
            if(!$result||($result==0)){
               $out['msg'][]='Problem with the database';
            }else{
                $out['succes']=true;
                 $out['msg'][]='All is good';
            }
        }
    }else{
        $out['msg'][]='You dont send any information';
    }
     echo json_encode($out);
     die();
    echo json_encode(['result'=>($_POST['value'])]);
    die();
}
function add_source_callback(){
    $out=['succes'=>false];
    if(!empty($_POST['value'])){
        $value=$_POST['value'];
        if(!empty($value['uri_sparql'])&&!empty($value['uri_base'])&&!empty($value['name_uri_base'])){
            $result=DP_SQL::DP_insert_source($value);
            if(!$result||($result==0)){
               $out['msg'][]='Problem with the database';
            }else{
                $out['succes']=true;
                 $out['msg'][]='All is good';
            }
        }
    }else{
        $out['msg'][]='You dont send any information';
    }
     echo json_encode($out);
     die();
}

function add_relation_user_source_callback(){
        $out=['succes'=>false];
    if(!empty($_POST['value'])){
        $value=$_POST['value'];

        if(!empty($value['id_user'])&&!empty($value['id_source'])){
            $result=DP_SQL::DP_insert_relation_user_source($value);
            if(!$result||($result==0)){
               $out['msg'][]='Problem with the database';
            }else{
                $out['succes']=true;
                 $out['msg'][]='All is good';
            }
        }
    }else{
        $out['msg'][]='You dont send any information';
    }
     echo json_encode($out);
     die();
}

function delete_relation_user_source_callback(){
       $out=['succes'=>false];
    if(!empty($_POST['value'])){
        $value=$_POST['value'];
        if(!empty($value['id'])){
            foreach ($value['id'] as $id) {
              $result=DP_SQL::DP_delete_relation_source_user($id);
            }            
            if(!$result||($result==0)){
               $out['msg'][]='Problem with the database';
            }else{
                $out['succes']=true;
                 $out['msg'][]='All is good';
            }
        }
    }else{
        $out['msg'][]='You dont send any information';
    }
     echo json_encode($out);
     die();
}
?>

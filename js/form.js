jQuery(document).ready(
        function() {
        //   jQuery('#wp-submit').hide();
jQuery(document).on('click',function(){
    check_mail();
});
jQuery('input').css('height','30px');

});
        
function check_mail(){

    var data = {
        action: 'verify_mail',
        value: {'user_mail':jQuery("#user_email").val(),'id_source':jQuery("#id_source :selected").val()}
    };
    console.log(data);
       jQuery.ajax({
        url: "wp-login.php",
        type: 'post',
        data: data,
        dataType: 'json'
    }).done(function(data){
        if(data.succes){
            console.log(data);
        }
    });
    
}

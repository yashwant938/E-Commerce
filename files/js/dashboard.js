$(document).ready(function() {
    $(".button-collapse").sidenav();
    $('.sidenav').sidenav();
});
$(".user").click(function() {
    $(".user-opt").stop().fadeToggle();
});




$('#nav_type').on('change', function(e) {
    if($("#nav_type").val()=="0"){
        $(".nav-content-box").css("display","none");
        $(".url-box").fadeIn();
//        $("#nav_content").attr("required",false);
//        $("#nav_url").attr("required",true);
    }
    else{
        $(".url-box").css("display","none");
        $(".nav-content-box").fadeIn();
//        $("#nav_content").attr("required",true);
//        $("#nav_url").attr("required",false);
    }
});

$('#select-type').on('change', function(e) {
    if($("#select-type").val()=="link"){
        $(".file-box").css("display","none");
        $(".url-box").fadeIn();
        $("#file-upload").attr("required",false);
        $("#url").attr("required",true);
    }
    else{
        $(".url-box").css("display","none");
        $(".file-box").fadeIn();
        $("#file-upload").attr("required",true);
        $("#url").attr("required",false);
    }
});
$("#mySwitch").change(function(){
    if($('#mySwitch').prop('checked')){
        $(".small-title").fadeIn();
        $("#input_text").attr("required",true);
    }
    else{
        $(".small-title").fadeOut();
        $("#input_text").attr("required",false);
    }
});

function dlt(to,id){
    $('#modal1').modal('open');
    $("#delete-true").click(function(){
        document.location="process/delete.php?"+to+"="+id;
    });
}
<script>
    //automatically scroll down to the latest message
    $('#messageBody').scrollTop($('#messageBody')[0].scrollHeight);

    //view members button
    $("#viewMembers").click(function(){
        $("#membersList").fadeToggle();
    });

    //addNewUser
    $("#addNewUser").click(function (){
        $("#addForm").fadeToggle();
    });
    
</script>
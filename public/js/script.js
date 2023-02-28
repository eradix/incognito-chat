//simple validation in the add user to a group chat
const submitAddUserForm = function(){
    let newUser = $("select#user_id").val();

    if (newUser) {
        $("#addUserInAGroupForm").submit();
    }
    else{
        alert("Please select user to add!");
    }
};
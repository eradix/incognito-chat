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

//image preview
//loading image preview selected
const readURL = (input, selector) => {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $(`#${selector}`).attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}

//trigger function when file value is change in create user
// $("#profile_image").change(function(){
//     readURL(this, 'user_image');
// });
const livePreview = function(element, selector){
    readURL(element, selector);
}
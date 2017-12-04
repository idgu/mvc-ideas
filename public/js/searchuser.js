/**
 * Created by idgu on 03.12.2017.
 */

function searchUser(nameInput) {
    const nameValue = nameInput.value;
    const liveSearch = document.querySelector('#liveSearch');
    var xhr = new XMLHttpRequest();

    liveSearch.innerHTML = '';
    if (nameValue != '') {
        xhr.open('GET', 'http://localhost/mvc-ideas/public/account/xhr-search-user-by-email?name=' + nameValue, true);

        xhr.onload = function() {
            if (this.status == 200) {
                console.log(this.responseText);
                users = JSON.parse(this.responseText);
                display = '';
                users.forEach(function(user) {
                    display += '<a href="http://localhost/mvc-ideas/public/admin/users/show/'+ user.id +'">'+user.name+'</a> <br>';
                });
                liveSearch.innerHTML = display;
            }


        }
        xhr.send();
    }
}



document.addEventListener('DOMContentLoaded', function () {
    const nameInput = document.querySelector('#inputName');


    nameInput.addEventListener('keyup', function () {
        searchUser(nameInput);
    })
});
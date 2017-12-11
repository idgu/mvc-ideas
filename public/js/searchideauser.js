/**
 * Created by idgu on 10.12.2017.
 */

function searchIdea(nameInput) {
    const nameValue = nameInput.value;
    const liveSearch = document.querySelector('#liveSearch');
    var xhr = new XMLHttpRequest();

    liveSearch.innerHTML = '';
    if (nameValue != '') {
        xhr.open('GET', 'http://localhost/mvc-ideas/public/account/xhr-search-idea-by-id?name=' + nameValue, true);

        xhr.onload = function() {
            if (this.status == 200) {
                console.log(this.responseText);
                ideas = JSON.parse(this.responseText);
                display = '';
                ideas.forEach(function(idea) {
                    display += '<a href="http://localhost/mvc-ideas/public/ideas/show/'+ idea.id +'">'+idea.name+'</a> <br>';
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
        searchIdea(nameInput);
    })
});
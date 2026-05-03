function del(id, btn){
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function(){
        if (this.readyState == 4 && this.status == 200) {
            if (xhr.responseText.trim() === "true"){
                var card = btn.closest('.card');
                if(card)
                    card.remove();
                else
                    alert("Sorry, something went wrong, please refresh the page");
            }
            else {
                alert("Sorry we couldn't delete it, try again");
            }
        }
    }
    xhr.open("GET","DeleteRecipe.php?id=" + id, true);
    xhr.send(null);
}
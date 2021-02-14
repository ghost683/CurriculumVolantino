
var page = 1;
var maxPage;
var filters = [];
var noMoreResult = false;

/**
 * load flyers on page ready
 */
document.addEventListener("DOMContentLoaded", function() {
    getFlyers();
});

/**
 * call EP for retrive flyer by his id
 */
function getFlyer() {
    //call EP for retrive flyer by id
    id = document.querySelector('#searchbox').value;
    ajax('GET', `http://localhost:4000/flyers/${id}.json`, (response) => {
        document.querySelector('#cardContainer').innerHTML = "";
        card = createCard(response.results);
        document.querySelector("#cardContainer").appendChild(card);
    }, (error) => {
        document.querySelector('#cardContainer').innerHTML = "Non c'è nessun volantino con l'id cercato.";
    })
    window.page = 1;
    window.noMoreResult = false;
}

/**
 * call ajax EP for retrive list of flyers
 * @param {number} page page number 
 * @param {number} limit resultset limit
 * @param {map} filters map off filters key=>value 
 */
function getFlyers(page = 1, limit = 4) {
    console.log(page, window.page);
    if(page < 1){
        window.page = 1;
    }
    if(window.maxPage > page){
        
        window.noMoreResult = false;
    }else if(window.noMoreResult){
        window.page = window.maxPage;
    }
    if(!window.noMoreResult){
        ajax('GET',`http://localhost:4000/flyers.json?page=${page}&limit=${limit}`, (response) => {
            document.querySelector('#cardContainer').innerHTML = "";
            response.results.forEach(element => {
                card = createCard(element);
                document.querySelector("#cardContainer").appendChild(card);
            });
        }, (error) => {
            document.querySelector('#cardContainer').innerHTML = "Non ci sono altri risultati da mostrare.";
            window.noMoreResult = true;
            window.maxPage = page;
        });
    }
}

/**
 * 
 * @param {string} method HTTP methos
 * @param {string} url  
 * @param {function} success callback function for success case
 * @param {function} error callback function for error case
 */
function ajax(method, url, success, error) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4) {
            if(this.status == 200){
                success(JSON.parse(this.response));
            }else {
                error(this);
            }
        }
    };
    xhttp.open(method, url , true);
    xhttp.send();
}

/**
 * clone card template and inject data
 * @param {object} element rappresenting ajax result
 * @return {HTMLElement} card to be injected in DOM
 */
function createCard(element){
    templateCard = document.querySelector('#cardTemplate');
    card = templateCard.cloneNode(true);
    card.setAttribute("id", element.id);
    card.querySelector(".cardTitle").innerHTML = element.retailer;
    card.querySelector(".card-text").innerHTML = element.title;
    card.querySelector(".blockquote-footer").innerHTML = "valido dal " + element.start_date + " al " + element.end_date;
    card.classList.remove("d-none");
    return card;
}
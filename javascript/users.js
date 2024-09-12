// seleziono la barra di ricerca (input e bottone) e la users list
const searchBar = document.querySelector(".search input"),
      searchIcon = document.querySelector(".search button"),
      usersList = document.querySelector(".users-list"); 

// se si clicca sull'icona di ricerca appare il campo di input e viene mostrato il pulsante per annullare la ricerca
searchIcon.onclick = () => {
  // mostro/nascondo la barra di ricerca
  searchBar.classList.toggle("show");
  // mostro/nascondo l'icona della ricerca
  searchIcon.classList.toggle("active");
  // focus barra di ricerca
  searchBar.focus();
  // se barra di ricerca attiva
  if (searchBar.classList.contains("active")) {
    // cancello il contenuto precedente nella barra di ricerca (faccio un reset della ricerca precedente)
    searchBar.value = "";
    // rimuovo classe active dalla barra di ricerca
    searchBar.classList.remove("active");
  }
}

// input nella barra di ricerca (quando l'utente scrive nome/cognome)
searchBar.onkeyup = () => {
  let searchTerm = searchBar.value; // ottengo valore digitato
  // se c'è testo allora aggiungo classe active
  if (searchTerm != "") {
    searchBar.classList.add("active");
  } else {
    // se non c'è testo rimuovo classe active
    searchBar.classList.remove("active");
  }

  // richiesta AJAX per cercare nuovi utenti (mentre si scrive nome/cognome appaiono i vari risultati in modo dinamico)
  let xhr = new XMLHttpRequest();
  xhr.open("POST", "php/search.php", true); // configura richiesta POST con file search.php
  xhr.onload = () => {
    // se richiesta completata allora posso aggiornare lista degli utenti
    if (xhr.readyState === XMLHttpRequest.DONE) {
      if (xhr.status === 200) {
        let data = xhr.response; // ottengo dati dal server
        usersList.innerHTML = data; // aggiorno lista utenti con i risultati della ricerca
      }
    }
  }
  // imposto tipo contenuto della richiesta
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  // invio al server il termine di ricerca
  xhr.send("searchTerm=" + searchTerm);
}

// ogni 500 millisecondi aggiorno lista utenti
setInterval(() => {
  let xhr = new XMLHttpRequest();
  // configuro richiesta GET per prendere lista utenti
  xhr.open("GET", "php/users.php", true);
  xhr.onload = () => {
    // se richiesta completata
    if (xhr.readyState === XMLHttpRequest.DONE) {
      if (xhr.status === 200) {
        let data = xhr.response; // ottengo dati dal server
        // aggiorno la lista utenti solo se la barra di ricerca non è più attiva
        if (!searchBar.classList.contains("active")) {
          usersList.innerHTML = data;
        }
      }
    }
  }
  xhr.send(); // invio richiesta al server
}, 500); // 500 millisecondi intervallo

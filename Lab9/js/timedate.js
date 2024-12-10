/**
  Funkcja gettheDate
  Pobiera aktualną datę i wyświetla ją w elemencie o ID "data".
 */
function gettheDate() {
    var Todays = new Date(); // Utworzenie obiektu z aktualną datą
    var TheDate = "" + (Todays.getMonth() + 1) + " / " + Todays.getDate() + " / " + (Todays.getFullYear()); // Formatowanie daty
    document.getElementById("data").innerHTML = TheDate; // Wyświetlenie daty w elemencie HTML
}

// Zmienne globalne do zarządzania zegarem
var timerID = null;      // ID timera
var timerRunning = false; // Flaga informująca, czy timer działa

/**
  Funkcja stopclock
  Zatrzymuje działający zegar.
 */
function stopclock() {
    if (timerRunning) {
        clearTimeout(timerID); // Zatrzymanie timera
    }
    timerRunning = false; 
}

/**
  Funkcja startclock
  Rozpoczyna działanie zegara, wyświetlając datę i czas.
 */
function startclock() {
    stopclock(); 
    gettheDate();
    showtime();   
}

/**
  Funkcja showtime
  Wyświetla aktualny czas w elemencie o ID "zegarek" i odświeża go co sekundę.
 */
function showtime() {
    var now = new Date();      
    var hours = now.getHours(); 
    var minutes = now.getMinutes(); 
    var seconds = now.getSeconds(); 

    // Formatowanie czasu
    var timeValue = "" + ((hours > 12) ? hours - 12 : hours); 
    timeValue += ((minutes < 10) ? ":0" : ":") + minutes; 
    timeValue += ((seconds < 10) ? ":0" : ":") + seconds; 
    timeValue += (hours >= 12) ? " P.M." : " A.M.";      

    // Wyświetlenie czasu w elemencie HTML
    document.getElementById("zegarek").innerHTML = timeValue;

    // Ustawienie timera do odświeżania czasu co 1 sekundę
    timerID = setTimeout("showtime()", 1000);
    timerRunning = true; 
}

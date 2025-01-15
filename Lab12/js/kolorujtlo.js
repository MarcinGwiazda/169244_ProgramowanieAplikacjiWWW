var computed = false;
var decimal = 0; 

/**
  Funkcja convert
  Przelicza wartość między dwoma jednostkami miary.
 */
function convert(entryform, from, to) {
    var convertfrom = from.selectedIndex; // Indeks wybranej jednostki wejściowej
    var convertto = to.selectedIndex;     // Indeks wybranej jednostki wyjściowej
    entryform.display.value = 
        (entryform.input.value * from[convertfrom].value / to[convertto].value);
}

/**
  Funkcja addchar
  Dodaje znak do pola wejściowego i wywołuje przeliczenie jednostek.
 */
function addchar(input, character) {
    if ((character == '.' && decimal == 0) || character != '.') {
        // Jeśli pole jest puste lub równe 0, ustaw znak. W przeciwnym razie dodaj do istniejącej wartości.
        (input.value == "" || input.value == "0") ? input.value = character : input.value += character;

        // Przelicz wartości
        convert(input.form, input.form.measure1, input.form.measure2);
        computed = true; // Ustawienie flagi na "przeliczono"

        if (character == '.') {
            decimal = 1; // Zaznaczenie, że liczba zawiera separator dziesiętny
        }
    }
}

/**
  Funkcja openVothcom
  Otwiera nową stronę w oknie popup.
 */
function openVothcom() {
    window.open("aktorzy.html", "Display window", "toolbar=no,directories=no,menubar=no");
}

/**
  Funkcja clear
  Czyści pola wejściowe i wynikowe oraz resetuje flagę separatora dziesiętnego.
 */
function clear(form) {
    form.input.value = 0;   
    form.display.value = 0; 
    decimal = 0; 
}

/**
  Funkcja changeBackground
  Zmienia tło strony na podany kolor.
 */
function changeBackground(hexNumber) {
    document.body.style.background = hexNumber;
}

'use strict';

function show_search() {
    let searchForm = document.getElementById("searchBar");
    let addForm = document.getElementById("addBar");
    searchForm.style.display = "block";
    addForm.style.display = "None";
}

function show_add() {
    let searchForm = document.getElementById("searchBar");
    let addForm = document.getElementById("addBar");
    searchForm.style.display = "None";
    addForm.style.display = "block";
}
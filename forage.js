'use strict';

function show_search() {
    let searchForm = document.getElementById("searchBar");
    let addForm = document.getElementById("addBar");
    searchForm.style.display = "block";
    addForm.style.display = "None";
    document.getElementById('searchSplit').style.minHeight = "90%";
    document.getElementById('searchSplit').style.maxHeight = "90%";
}

function show_add() {
    let searchForm = document.getElementById("searchBar");
    let addForm = document.getElementById("addBar");
    searchForm.style.display = "None";
    addForm.style.display = "block";
    document.getElementById('searchSplit').style.minHeight = "65%";
    document.getElementById('searchSplit').style.maxHeight = "65%";
}
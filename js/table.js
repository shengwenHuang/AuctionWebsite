var table_rows = document.getElementsByClassName("table-row");
for (i = 0; i < table_rows.length; i++) {
    table_rows[i].addEventListener("click", function (e) {
        var target = (e.target) ? e.target : e.srcElement;
        window.location.href = target.parentNode.getAttribute("data-href");
    })
}
document.addEventListener("DOMContentLoaded", function () {
    const table = document.querySelector("table");
    const headers = table.querySelectorAll("th");
    const tbody = table.querySelector("tbody");

    const getCellValue = (row, index) => row.cells[index].innerText || row.cells[index].textContent;
    
    const comparer = (idx, asc) => (a, b) => {
        const valA = getCellValue(asc ? a : b, idx);
        const valB = getCellValue(asc ? b : a, idx);
        
        return isNaN(valA) || isNaN(valB) ? valA.localeCompare(valB) : valA - valB;
    };

    headers.forEach((header, index) => {
        header.style.cursor = "pointer";
        header.style.position = "relative";
        header.innerHTML += " <span class='sort-indicator'>⇅</span>";
        
        header.addEventListener("click", () => {
            const rows = Array.from(tbody.querySelectorAll("tr"));
            const asc = header.dataset.order === "desc";
            header.dataset.order = asc ? "asc" : "desc";
            
            rows.sort(comparer(index, asc));
            tbody.append(...rows);

            headers.forEach(h => h.querySelector(".sort-indicator").innerText = "⇅");
            header.querySelector(".sort-indicator").innerText = asc ? "↑" : "↓";
        });
    });
});

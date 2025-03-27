document.addEventListener("DOMContentLoaded", function () {
    const dropdowns = document.querySelectorAll(".dropdown");

    // Função para fechar todos os dropdowns
    function closeAllDropdowns() {
        dropdowns.forEach((d) => {
            const btn = d.querySelector(".dropbtn");
            const cont = d.querySelector(".dropdown-content");
            const items = cont.querySelectorAll(".dropdown-item");
            
            cont.classList.remove("show");
            btn.classList.remove("active");
            
            items.forEach((item) => {
                item.style.opacity = "0";
                item.style.transform = "translateY(-10px)";
            });
        });
    }

    dropdowns.forEach((dropdown) => {
        const button = dropdown.querySelector(".dropbtn");
        const content = dropdown.querySelector(".dropdown-content");
        const items = content.querySelectorAll(".dropdown-item");

        button.addEventListener("click", (event) => {
            event.stopPropagation();
            
            // Se este dropdown não está aberto, fecha todos primeiro
            if (!content.classList.contains("show")) {
                closeAllDropdowns();
            }
            
            // Toggle do dropdown atual
            content.classList.toggle("show");
            button.classList.toggle("active");

            if (content.classList.contains("show")) {
                items.forEach((item, index) => {
                    setTimeout(() => {
                        item.style.opacity = "1";
                        item.style.transform = "translateY(0)";
                    }, index * 100);
                });
            } else {
                items.forEach((item) => {
                    item.style.opacity = "0";
                    item.style.transform = "translateY(-10px)";
                });
            }
        });
    });

    window.addEventListener("click", (event) => {
        if (!event.target.closest('.dropdown')) {
            closeAllDropdowns();
        }
    });
});
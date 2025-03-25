document.addEventListener("DOMContentLoaded", function () {
    const dropdowns = document.querySelectorAll(".dropdown");

    dropdowns.forEach((dropdown) => {
        const button = dropdown.querySelector(".dropbtn");
        const content = dropdown.querySelector(".dropdown-content");
        const items = content.querySelectorAll(".dropdown-item");

        button.addEventListener("click", (event) => {
            // Previne que o clique se propague para o window
            event.stopPropagation();
            
            // Toggle do dropdown
            content.classList.toggle("show");
            button.classList.toggle("active");

            if (content.classList.contains("show")) {
                // Animação de fade-in para cada item
                items.forEach((item, index) => {
                    setTimeout(() => {
                        item.style.opacity = "1";
                        item.style.transform = "translateY(0)";
                    }, index * 100);
                });
            } else {
                // Resetar a animação ao fechar
                items.forEach((item) => {
                    item.style.opacity = "0";
                    item.style.transform = "translateY(-10px)";
                });
            }
        });
    });

    // Fechar o dropdown ao clicar fora
    window.addEventListener("click", (event) => {
        dropdowns.forEach((dropdown) => {
            const button = dropdown.querySelector(".dropbtn");
            const content = dropdown.querySelector(".dropdown-content");
            const items = content.querySelectorAll(".dropdown-item");

            if (!dropdown.contains(event.target)) {
                content.classList.remove("show");
                button.classList.remove("active");

                items.forEach((item) => {
                    item.style.opacity = "0";
                    item.style.transform = "translateY(-10px)";
                });
            }
        });
    });
});
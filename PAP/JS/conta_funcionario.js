document.addEventListener("DOMContentLoaded", function () {
    // Função para exibir dropdown
    const setupDropdowns = () => {
        const dropdowns = document.querySelectorAll(".dropdown");

        dropdowns.forEach((dropdown) => {
            const button = dropdown.querySelector(".dropbtn");
            const content = dropdown.querySelector(".dropdown-content");
            const items = content.querySelectorAll(".dropdown-item");

            button.addEventListener("click", (event) => {
                event.stopPropagation();
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

        // Fechar dropdown quando clicar fora
        window.addEventListener("click", (event) => {
            if (!event.target.closest('.dropdown')) {
                dropdowns.forEach((dropdown) => {
                    const button = dropdown.querySelector(".dropbtn");
                    const content = dropdown.querySelector(".dropdown-content");
                    const items = content.querySelectorAll(".dropdown-item");

                    if (content.classList.contains("show")) {
                        content.classList.remove("show");
                        button.classList.remove("active");

                        items.forEach((item) => {
                            item.style.opacity = "0";
                            item.style.transform = "translateY(-10px)";
                        });
                    }
                });
            }
        });
    };

    // Modal para alterar senha
    const setupPasswordModal = () => {
        const modal = document.getElementById("passwordModal");
        const btn = document.getElementById("btnChangePassword");
        const closeBtn = document.querySelector(".close-modal");
        const form = document.getElementById("changePasswordForm");
        const passwordError = document.getElementById("passwordError");
        
        // Abrir modal
        btn.addEventListener("click", function(e) {
            e.preventDefault();
            modal.style.display = "block";
            
            // Manter a rolagem disponível, mas rolar para o modal
            const modalContent = modal.querySelector(".modal-content");
            if (modalContent) {
                // Scroll para o modal estar visível
                modalContent.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
        
        // Fechar modal
        closeBtn.addEventListener("click", function() {
            modal.style.display = "none";
            form.reset();
            passwordError.style.display = "none";
        });
        
        // Fechar ao clicar fora
        window.addEventListener("click", function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
                form.reset();
                passwordError.style.display = "none";
            }
        });
        
        // Evitar fechar o modal ao clicar dentro do conteúdo do modal
        modal.querySelector(".modal-content").addEventListener("click", function(e) {
            e.stopPropagation();
        });
        
        // Validar formulário e enviar via AJAX
        form.addEventListener("submit", function(e) {
            e.preventDefault();
            
            const currentPassword = document.getElementById("currentPassword").value;
            const newPassword = document.getElementById("newPassword").value;
            const confirmPassword = document.getElementById("confirmPassword").value;
            
            // Validar campos
            if (!currentPassword || !newPassword || !confirmPassword) {
                showError("Por favor, preencha todos os campos.");
                return;
            }
            
            // Validar se as senhas coincidem
            if (newPassword !== confirmPassword) {
                showError("As senhas não coincidem.");
                return;
            }
            
            // Validar comprimento mínimo
            if (newPassword.length < 6) {
                showError("A senha deve ter pelo menos 6 caracteres.");
                return;
            }
            
            // Mostrar indicador de loading
            const submitBtn = form.querySelector("button[type='submit']");
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.innerHTML = "Processando...";
            submitBtn.disabled = true;
            
            // Criar objeto FormData para enviar os dados
            const formData = new FormData();
            formData.append("currentPassword", currentPassword);
            formData.append("newPassword", newPassword);
            
            // Enviar requisição para o servidor
            fetch("../PHP/alterar_senha.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Restaurar botão
                submitBtn.innerHTML = originalBtnText;
                submitBtn.disabled = false;
                
                if (data.success) {
                    // Sucesso
                    alert(data.message);
                    modal.style.display = "none";
                    form.reset();
                } else {
                    // Erro
                    showError(data.message);
                }
            })
            .catch(error => {
                // Erro na requisição
                submitBtn.innerHTML = originalBtnText;
                submitBtn.disabled = false;
                showError("Erro ao processar a requisição. Tente novamente.");
                console.error("Erro:", error);
            });
        });
        
        function showError(message) {
            passwordError.textContent = message;
            passwordError.style.display = "block";
        }
    };

    // Inicializar funcionalidades
    setupDropdowns();
    setupPasswordModal();
}); 
// Menu burger
document.addEventListener("DOMContentLoaded", ()=> {
    const burger = document.getElementById("burger");
    const navbar = document.getElementById("navbar-header");

    if (burger && navbar) {
        burger.addEventListener("click", ()=> {
            navbar.classList.toggle("show");
        });
    }
});

//Snack Shop
document.addEventListener("DOMContentLoaded", () => {
    const quantityInputs = document.querySelectorAll(".shop_quantity_input");
    const totalItemsSpan = document.querySelector("#total-items");
    const totalPriceSpan = document.querySelector("#total-price");

    function updateAll() {
        let totalItems = 0;
        let totalPrice = 0;

        quantityInputs.forEach(input => {
            const qty = parseInt(input.value) || 0;
            const price = parseFloat(input.dataset.price) || 0;

            totalItems += qty;
            totalPrice += qty * price;

            const card = input.closest(".product-card");
            const localDisplay = card.querySelector(".selected-count span");
            localDisplay.textContent = qty;
        });

        totalItemsSpan.textContent = totalItems;
        totalPriceSpan.textContent = totalPrice.toFixed(2);
    }

    quantityInputs.forEach(input => {
        input.addEventListener("input", updateAll);
    });

    updateAll(); // initial update
});

// Confirmation lors de l'achat et listing des produits séléctionnés
function confirmOrder() {
    const selectedProducts = [];
    const inputs = document.querySelectorAll('.shop_quantity_input');

    inputs.forEach(input => {
        const quantity = parseInt(input.value);
        if (quantity > 0) {
            const label = input.parentElement.querySelector('h3').innerText;
            selectedProducts.push(`${label} × ${quantity}`);
        }
    });

    if (selectedProducts.length === 0) {
        alert('⚠️ You must select at least one product.');
        return false; // Empêche l'envoi du formulaire
    }

    const summary = selectedProducts.join('\n');
    return confirm(`🛒 Confirm your order:\n\n${summary}`);
}

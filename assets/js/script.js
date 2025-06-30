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

    // Sélection des éléments du résumé flottant
    const liveSummary = document.getElementById("live-summary");
    const floatingSummary = document.getElementById("floating-summary");
    const floatingItems = document.getElementById("floating-items");
    const floatingPrice = document.getElementById("floating-price");


    quantityInputs.forEach(input => {
        input.addEventListener("change", () => {
            const productId = input.id.split("_")[1];
            const quantity = parseInt(input.value) || 0;

            fetch("ajax/save_quantity.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: `product_id=${productId}&quantity=${quantity}`
            })
                .then(res => res.json())
                .then(data => {
                    console.log("✅ Réponse AJAX :", data);
                    if (data.success) {
                        updateSummary();
                    } else {
                        console.error("Erreur AJAX :", data.message);
                    }
                })
                .catch(error => console.error("Erreur fetch :", error));
        });
    });


    updateSummary(); // initial update au chargement de la page

    // Intersection Observer pour cacher/afficher le résumé flottant
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                floatingSummary.classList.add("hidden");
            } else {
                floatingSummary.classList.remove("hidden");
            }
        });
    });

    observer.observe(liveSummary);
});

// Confirmation lors de l'achat et listing des produits séléctionnés
function confirmOrder() {
    return fetch("ajax/get_cart_summary.php")
        .then(res => res.json())
        .then(data => {
            if (data.items === 0) {
                alert('⚠️ You must select at least one product.');
                return false;
            }

            const summary = data.list.split(', ').join('\n');
            return confirm(`🛒 Confirm your order:\n\n${summary}`);
        })
        .catch(error => {
            console.error("Erreur dans confirmOrder() :", error);
            alert("❌ Error confirming your order.");
            return false;
        });
}


// Gestion du panier en direct avec AJAX (pas le nettoyant pour chiottes ...)
function updateSummary() {
    fetch("ajax/get_cart_summary.php")
        .then(res => res.json())
        .then(data => {
            // Résumé principal
            document.getElementById('total-items').textContent = data.items;
            document.getElementById('total-price').textContent = data.price;
            document.getElementById('selected-items').innerHTML = data.list;
            document.getElementById('selected-items').innerHTML = data.list_html;

// Résumé flottant
            document.getElementById('floating-items').textContent = data.items;
            document.getElementById('floating-price').textContent = data.price;
            document.getElementById('floating-selected-items').innerHTML = data.list;
            document.getElementById('floating-selected-items').innerHTML = data.list_html;
        })
        .catch(error => {
            console.error("Erreur fetch résumé global :", error);
        });

    // Optionnel : mettre à jour les "Selected: x" pour les produits affichés
    document.querySelectorAll('.shop_quantity_input').forEach(input => {
        const quantity = parseInt(input.value) || 0;
        const card = input.closest('.product-card');
        const span = card.querySelector('.selected-count span');
        if (span) span.textContent = quantity;
    });
}

// Clear cart
document.addEventListener('DOMContentLoaded', () => {
    const clearBtn = document.getElementById('clear-cart');
    if (clearBtn) {
        clearBtn.addEventListener('click', () => {
            if (confirm('🧹 Are you sure you want to clear the cart?')) {
                fetch('ajax/clear_cart.php')
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            // Réinitialise les inputs visibles
                            document.querySelectorAll('.shop_quantity_input').forEach(input => {
                                input.value = 0;
                            });

                            updateSummary(); // met à jour les résumés
                        } else {
                            alert("❌ Failed to clear cart.");
                        }
                    })
                    .catch(err => {
                        console.error("Erreur AJAX clear_cart:", err);
                        alert("❌ Error clearing cart.");
                    });
            }
        });
    }
});

// Confirm Order
document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector('form[action="order/process_order.php"]');
    const submitBtn = document.getElementById("submit-order");

    if (form && submitBtn) {
        form.addEventListener("submit", (e) => {
            e.preventDefault(); // bloque la soumission le temps de vérifier

            fetch("ajax/get_cart_summary.php")
                .then(res => res.json())
                .then(data => {
                    if (data.items === 0) {
                        alert('⚠️ You must select at least one product.');
                        return;
                    }

                    const summary = data.list_text.split('\n').map(item => '• ' + item).join('\n');
                    if (confirm(`🛒 Confirm your order:\n\n${summary}`)) {
                        form.submit();
                    }

                })
                .catch(error => {
                    console.error("Erreur dans confirmOrder() :", error);
                    alert("❌ Error confirming your order.");
                });
        });
    }
});

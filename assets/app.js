/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// start the Stimulus application


// enable the interactive UI components from Flowbite with Turbo
import 'flowbite/dist/flowbite.turbo.js';

document.addEventListener('DOMContentLoaded', initPasswordToggles);
document.addEventListener('turbo:load', initPasswordToggles);

function initPasswordToggles() {
    document.querySelectorAll('[data-password-toggle]').forEach((wrapper) => {
        const input = wrapper.querySelector('input');
        const button = wrapper.querySelector('[data-password-toggle-button]');
        const showIcon = wrapper.querySelector('[data-password-icon-show]');
        const hideIcon = wrapper.querySelector('[data-password-icon-hide]');

        if (!input || !button) {
            return;
        }

        const syncState = () => {
            const isHidden = input.type === 'password';
            button.setAttribute('aria-label', isHidden ? 'Afficher le mot de passe' : 'Masquer le mot de passe');
            button.setAttribute('title', isHidden ? 'Afficher le mot de passe' : 'Masquer le mot de passe');

            if (showIcon) {
                showIcon.classList.toggle('hidden', !isHidden);
            }

            if (hideIcon) {
                hideIcon.classList.toggle('hidden', isHidden);
            }
        };

        button.addEventListener('click', () => {
            input.type = input.type === 'password' ? 'text' : 'password';
            syncState();
        });

        syncState();
    });
}

window.addToCart = async function addToCart(livreId) {
    const button = document.getElementById('add-to-cart-btn');
    if (!button) {
        return;
    }

    if (button.dataset.authenticated !== 'true') {
        window.location.href = button.dataset.guestUrl;
        return;
    }

    const originalContent = button.innerHTML;
    button.disabled = true;
    button.classList.add('opacity-70', 'cursor-not-allowed');
    button.innerHTML = 'Ajout en cours...';

    try {
        const response = await fetch(`/api/panier/add/${livreId}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (response.status === 401) {
            window.location.href = button.dataset.guestUrl;
            return;
        }

        const data = await response.json();
        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Erreur lors de l\'ajout au panier.');
        }

        updateCartBadge(data.count);

        button.innerHTML = 'Livre ajoute';
        setTimeout(() => {
            button.innerHTML = originalContent;
            button.disabled = false;
            button.classList.remove('opacity-70', 'cursor-not-allowed');
        }, 1200);
    } catch (error) {
        console.error(error);
        button.innerHTML = originalContent;
        button.disabled = false;
        button.classList.remove('opacity-70', 'cursor-not-allowed');
    }
};

function updateCartBadge(count) {
    const cartLink = document.getElementById('cart-link');
    if (!cartLink || count <= 0) {
        return;
    }

    let badge = document.getElementById('cart-count-badge');
    if (!badge) {
        badge = document.createElement('span');
        badge.id = 'cart-count-badge';
        badge.className = 'absolute -top-2 -right-2 bg-red-600 text-white text-[10px] font-bold w-5 h-5 flex items-center justify-center rounded-full ring-2 ring-[--bluegray-deep]';
        cartLink.appendChild(badge);
    }

    badge.textContent = String(count);
}

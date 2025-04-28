/**
 * AgroInnovate - Language Switcher JavaScript
 * Handles the language switching functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize language from session
    const currentLang = document.documentElement.lang || 'en';
    updatePageLanguage(currentLang);

    // Add click handlers to language selector links
    document.querySelectorAll('.language-selector-floating .dropdown-item').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const lang = this.href.split('language=')[1];
            switchLanguage(lang);
        });
    });
});

/**
 * Switches the language and updates the UI
 * @param {string} lang - The language code to switch to ('en' or 'hi')
 */
function switchLanguage(lang) {
    showLanguageLoadingIndicator();

    // First update the UI immediately
    updatePageLanguage(lang);

    // Then make the server request
    fetch(`includes/update_language.php?language=${lang}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Cache-Control': 'no-cache'
        }
    })
    .then(response => {
        hideLanguageLoadingIndicator();
        showLanguageChangeNotification(lang);
    })
    .catch(error => {
        console.error('Error changing language:', error);
        hideLanguageLoadingIndicator();
    });
}

/**
 * Updates all translatable elements on the page
 * @param {string} lang - The language code ('en' or 'hi')
 */
function updatePageLanguage(lang) {
    // Update html lang attribute
    document.documentElement.lang = lang;

    // Update all elements with data-lang-en and data-lang-hi attributes
    document.querySelectorAll('[data-lang-en][data-lang-hi]').forEach(element => {
        const text = element.getAttribute(`data-lang-${lang}`);
        if (text) {
            if (element.tagName.toLowerCase() === 'input' && element.type === 'submit') {
                element.value = text;
            } else {
                element.textContent = text;
            }
        }
    });

    // Update all elements with old data-en and data-hi attributes (for backward compatibility)
    document.querySelectorAll('[data-en][data-hi]').forEach(element => {
        const text = element.getAttribute(`data-${lang}`);
        if (text) {
            if (element.tagName.toLowerCase() === 'input' && element.type === 'submit') {
                element.value = text;
            } else {
                element.textContent = text;
            }
        }
    });

    // Update page title if it has language attributes
    const titleElement = document.querySelector('title');
    if (titleElement && titleElement.getAttribute(`data-lang-${lang}`)) {
        titleElement.textContent = titleElement.getAttribute(`data-lang-${lang}`);
    }

    // Store the language preference
    localStorage.setItem('preferred_language', lang);
}

/**
 * Shows a loading indicator while changing language
 */
function showLanguageLoadingIndicator() {
    // Create loading indicator if it doesn't exist
    if (!document.getElementById('language-loading')) {
        const loader = document.createElement('div');
        loader.id = 'language-loading';
        loader.innerHTML = `
            <div class="language-loading-overlay">
                <div class="language-loading-spinner"></div>
                <div class="language-loading-text">${getCurrentLanguage() === 'en' ? 'Changing language...' : 'भाषा बदल रही है...'}</div>
            </div>
        `;
        document.body.appendChild(loader);
        
        // Add styles if not already added
        if (!document.getElementById('language-loading-styles')) {
            const style = document.createElement('style');
            style.id = 'language-loading-styles';
            style.textContent = `
                .language-loading-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0, 0, 0, 0.5);
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center;
                    z-index: 9999;
                }
                .language-loading-spinner {
                    border: 4px solid rgba(255, 255, 255, 0.3);
                    border-radius: 50%;
                    border-top: 4px solid #ffffff;
                    width: 40px;
                    height: 40px;
                    animation: spin 1s linear infinite;
                    margin-bottom: 10px;
                }
                .language-loading-text {
                    color: white;
                    font-size: 16px;
                    font-weight: bold;
                }
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
            `;
            document.head.appendChild(style);
        }
    }
    document.getElementById('language-loading').style.display = 'block';
}

/**
 * Hides the language loading indicator
 */
function hideLanguageLoadingIndicator() {
    const loader = document.getElementById('language-loading');
    if (loader) {
        loader.style.display = 'none';
    }
}

/**
 * Shows a notification when language is changed
 * @param {string} lang - The language code that was switched to
 */
function showLanguageChangeNotification(lang) {
    const messages = {
        en: {
            changed: 'Language changed to English'
        },
        hi: {
            changed: 'भाषा हिंदी में बदल गई है'
        }
    };

    const notification = document.createElement('div');
    notification.className = 'language-notification';
    notification.textContent = messages[lang].changed;

    // Add styles if not already added
    if (!document.getElementById('language-notification-styles')) {
        const style = document.createElement('style');
        style.id = 'language-notification-styles';
        style.textContent = `
            .language-notification {
                position: fixed;
                bottom: 20px;
                left: 50%;
                transform: translateX(-50%);
                background-color: #4CAF50;
                color: white;
                padding: 12px 24px;
                border-radius: 4px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
                z-index: 9999;
                animation: slideUp 0.3s ease-out forwards;
                font-size: 16px;
            }
            @keyframes slideUp {
                from {
                    transform: translate(-50%, 100%);
                    opacity: 0;
                }
                to {
                    transform: translate(-50%, 0);
                    opacity: 1;
                }
            }
        `;
        document.head.appendChild(style);
    }

    document.body.appendChild(notification);

    // Remove notification after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'fadeOut 0.3s ease-out forwards';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

/**
 * Gets the current language from the HTML lang attribute
 * @returns {string} The current language code
 */
function getCurrentLanguage() {
    return document.documentElement.lang || 'en';
}

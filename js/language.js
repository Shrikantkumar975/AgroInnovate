/**
 * AgroInnovate - Language Switch JavaScript
 * Handles the language switching functionality
 */

// Document ready function
document.addEventListener('DOMContentLoaded', function() {
    // Set up language toggle button
    const languageToggle = document.getElementById('language-toggle');
    if (languageToggle) {
        languageToggle.addEventListener('click', function() {
            toggleLanguage();
        });
    }
});

/**
 * Toggles between English and Hindi
 */
function toggleLanguage() {
    // Get the current language
    const currentLang = document.documentElement.lang;
    
    // Switch to other language
    const newLang = currentLang === 'en' ? 'hi' : 'en';
    
    // Update language via AJAX
    fetch(`/includes/set_language.php?lang=${newLang}`, {
        method: 'GET'
    })
    .then(response => {
        if (response.ok) {
            // Refresh the page to apply the language change
            window.location.reload();
        }
    })
    .catch(error => {
        console.error('Error changing language:', error);
        
        // If AJAX fails, try to switch language on client side
        switchLanguageClientSide(newLang);
    });
}

/**
 * Switches language on client side (fallback if AJAX fails)
 * 
 * @param {string} lang - The language code to switch to
 */
function switchLanguageClientSide(lang) {
    // Update HTML lang attribute
    document.documentElement.lang = lang;
    
    // Update language toggle button
    const languageToggle = document.getElementById('language-toggle');
    if (languageToggle) {
        languageToggle.textContent = lang === 'en' ? 'हिंदी' : 'English';
    }
    
    // Update all elements with data-en and data-hi attributes
    document.querySelectorAll('[data-en][data-hi]').forEach(element => {
        element.textContent = element.getAttribute(`data-${lang}`);
    });
    
    // Update form placeholders
    document.querySelectorAll('input[data-placeholder-en][data-placeholder-hi]').forEach(input => {
        input.placeholder = input.getAttribute(`data-placeholder-${lang}`);
    });
    
    // Update form buttons
    document.querySelectorAll('button[data-text-en][data-text-hi]').forEach(button => {
        button.textContent = button.getAttribute(`data-text-${lang}`);
    });
}

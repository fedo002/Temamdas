// Custom Translation System
class CustomTranslationSystem {
    constructor() {
      this.currentLanguage = 'tr'; // Default language (Turkish)
      this.availableLanguages = ['tr', 'en', 'de', 'fr', 'ru', 'ar']; // Available languages
      this.translations = {};
      this.initialized = false;
    }
  
    async init() {
      if (this.initialized) return;
      
      try {
        // Load all language files
        for (const lang of this.availableLanguages) {
          const response = await fetch(`/assets/lang/${lang}.json`);
          const data = await response.json();
          this.translations[lang] = data;
        }
        
        this.initialized = true;
        
        // Set initial language from localStorage or use default
        const savedLang = localStorage.getItem('preferredLanguage');
        if (savedLang && this.availableLanguages.includes(savedLang)) {
          this.currentLanguage = savedLang;
        }
        
        // Apply translations immediately
        this.applyTranslations();
        
        // Create UI elements
        this.createUI();
      } catch (error) {
        console.error('Failed to initialize translation system:', error);
      }
    }
  
    createUI() {
      // Create language selector dropdown
      const langSelector = document.createElement('div');
      langSelector.className = 'language-selector';
      
      // Create dropdown toggle
      const toggle = document.createElement('button');
      toggle.className = 'lang-toggle';
      toggle.innerHTML = `
        <span class="current-lang">${this.currentLanguage.toUpperCase()}</span>
        <svg width="10" height="6" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      `;
      
      // Create dropdown menu
      const dropdown = document.createElement('div');
      dropdown.className = 'lang-dropdown';
      
      this.availableLanguages.forEach(lang => {
        const langOption = document.createElement('button');
        langOption.className = 'lang-option';
        langOption.textContent = lang.toUpperCase();
        langOption.dataset.lang = lang;
        
        langOption.addEventListener('click', () => {
          this.setLanguage(lang);
          toggle.querySelector('.current-lang').textContent = lang.toUpperCase();
          dropdown.classList.remove('active');
        });
        
        dropdown.appendChild(langOption);
      });
      
      // Toggle dropdown on click
      toggle.addEventListener('click', () => {
        dropdown.classList.toggle('active');
      });
      
      // Close dropdown when clicking outside
      document.addEventListener('click', (e) => {
        if (!langSelector.contains(e.target)) {
          dropdown.classList.remove('active');
        }
      });
      
      langSelector.appendChild(toggle);
      langSelector.appendChild(dropdown);
      
      // Add to navbar or mobile menu
      const navbar = document.querySelector('.navbar-nav');
      const mobileMenu = document.querySelector('.mobile-menu');
      
      if (navbar) {
        const navItem = document.createElement('li');
        navItem.className = 'nav-item language-nav-item';
        navItem.appendChild(langSelector);
        navbar.appendChild(navItem);
      }
      
      if (mobileMenu) {
        const mobileSelector = langSelector.cloneNode(true);
        mobileSelector.classList.add('mobile-language-selector');
        
        // Add event listeners to the clone
        mobileSelector.querySelector('.lang-toggle').addEventListener('click', () => {
          mobileSelector.querySelector('.lang-dropdown').classList.toggle('active');
        });
        
        mobileSelector.querySelectorAll('.lang-option').forEach(option => {
          option.addEventListener('click', () => {
            const lang = option.dataset.lang;
            this.setLanguage(lang);
            mobileSelector.querySelector('.current-lang').textContent = lang.toUpperCase();
            mobileSelector.querySelector('.lang-dropdown').classList.remove('active');
          });
        });
        
        mobileMenu.appendChild(mobileSelector);
      }
    }
  
    setLanguage(langCode) {
      if (!this.availableLanguages.includes(langCode)) {
        console.error(`Language '${langCode}' is not available`);
        return;
      }
      
      this.currentLanguage = langCode;
      localStorage.setItem('preferredLanguage', langCode);
      this.applyTranslations();
      
      // Dispatch event for components that need to react to language changes
      document.dispatchEvent(new CustomEvent('languageChanged', { detail: { language: langCode } }));
    }
  
    applyTranslations() {
      if (!this.initialized) {
        console.warn('Translation system not initialized yet');
        return;
      }
      
      const translations = this.translations[this.currentLanguage];
      
      document.querySelectorAll('[data-i18n]').forEach(element => {
        const key = element.getAttribute('data-i18n');
        
        if (translations[key]) {
          // Handle attributes
          if (element.hasAttribute('data-i18n-attr')) {
            const attr = element.getAttribute('data-i18n-attr');
            element.setAttribute(attr, translations[key]);
          } 
          // Handle HTML content
          else if (element.hasAttribute('data-i18n-html')) {
            element.innerHTML = translations[key];
          } 
          // Handle text content (default)
          else {
            element.textContent = translations[key];
          }
        }
      });
    }
  
    translate(key) {
      if (!this.initialized) return key;
      
      const translations = this.translations[this.currentLanguage];
      return translations[key] || key;
    }
  }
  
  // Initialize translation system
  const translationSystem = new CustomTranslationSystem();
  document.addEventListener('DOMContentLoaded', () => {
    translationSystem.init();
  });
  
  // Export for global access
  window.translationSystem = translationSystem;
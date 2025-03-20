/**
 * Translation System for Mobile WebView App
 * Handles multilingual support for the application
 */

// Translation Manager Class
class TranslationManager {
    constructor() {
      this.currentLanguage = 'en';
      this.translations = {};
      this.defaultLanguage = 'en';
      this.isLoading = false;
      this.supportedLanguages = [
        'ar', 'bn', 'de', 'en', 'es', 'fr', 'hi', 'id', 'ja', 
        'ka', 'ko', 'pt', 'ru', 'tr', 'ur', 'vi', 'zh'
      ];
      
      this.languageNames = {
        'ar': 'العربية',
        'bn': 'বাংলা',
        'de': 'Deutsch',
        'en': 'English',
        'es': 'Español',
        'fr': 'Français',
        'hi': 'हिन्दी',
        'id': 'Bahasa Indonesia',
        'ja': '日本語',
        'ka': 'ქართული',
        'ko': '한국어',
        'pt': 'Português',
        'ru': 'Русский',
        'tr': 'Türkçe',
        'ur': 'اردو',
        'vi': 'Tiếng Việt',
        'zh': '中文'
      };
      
      // RTL languages
      this.rtlLanguages = ['ar', 'ur'];
      
      // Initialize
      this.init();
    }
    
    init() {
      // Get language from URL parameter, cookie, or browser
      this.detectLanguage();
      
      // Load language data
      this.loadLanguage(this.currentLanguage);
      
      // Apply translations after page load
      document.addEventListener('DOMContentLoaded', () => {
        this.translatePage();
        this.setupLanguageDirection();
      });
    }
    
    detectLanguage() {
      // Check URL parameter
      const urlParams = new URLSearchParams(window.location.search);
      const langParam = urlParams.get('lang');
      
      if (langParam && this.supportedLanguages.includes(langParam)) {
        this.currentLanguage = langParam;
        // Save to cookie for persistence
        this.setCookie('selected_language', langParam, 365);
        return;
      }
      
      // Check cookie
      const cookieLang = this.getCookie('selected_language');
      if (cookieLang && this.supportedLanguages.includes(cookieLang)) {
        this.currentLanguage = cookieLang;
        return;
      }
      
      // Use browser language as fallback
      const browserLang = navigator.language.split('-')[0];
      if (browserLang && this.supportedLanguages.includes(browserLang)) {
        this.currentLanguage = browserLang;
        return;
      }
      
      // Default to English
      this.currentLanguage = this.defaultLanguage;
    }
    
    setCookie(name, value, days) {
      const expires = new Date();
      expires.setTime(expires.getTime() + days * 24 * 60 * 60 * 1000);
      document.cookie = `${name}=${value};expires=${expires.toUTCString()};path=/;SameSite=Lax`;
    }
    
    getCookie(name) {
      const cookies = document.cookie.split(';');
      for (let i = 0; i < cookies.length; i++) {
        const cookie = cookies[i].trim();
        if (cookie.startsWith(name + '=')) {
          return cookie.substring(name.length + 1);
        }
      }
      return null;
    }
    
    async loadLanguage(lang) {
      if (this.isLoading) return;
      
      // Check if already loaded
      if (this.translations[lang]) {
        this.currentLanguage = lang;
        this.translatePage();
        this.setupLanguageDirection();
        return;
      }
      
      this.isLoading = true;
      
      try {
        // Load language file
        const response = await fetch(`/mobile/assets/lang/${lang}.json`);
        if (!response.ok) {
          throw new Error(`Failed to load ${lang} language file`);
        }
        
        const data = await response.json();
        this.translations[lang] = data;
        this.currentLanguage = lang;
        
        // Translate page with new language
        this.translatePage();
        this.setupLanguageDirection();
        
        // Save preference to cookie
        this.setCookie('selected_language', lang, 365);
        
        console.log(`Language loaded: ${lang}`);
      } catch (error) {
        console.error('Error loading language:', error);
        
        // Fallback to default language if not already there
        if (lang !== this.defaultLanguage) {
          console.log(`Falling back to ${this.defaultLanguage}`);
          
          if (!this.translations[this.defaultLanguage]) {
            await this.loadLanguage(this.defaultLanguage);
          } else {
            this.currentLanguage = this.defaultLanguage;
            this.translatePage();
            this.setupLanguageDirection();
          }
        }
      } finally {
        this.isLoading = false;
      }
    }
    
    // Change language on user request
    changeLanguage(lang) {
      if (!this.supportedLanguages.includes(lang)) {
        console.error(`Language ${lang} is not supported`);
        return;
      }
      
      this.loadLanguage(lang);
    }
    
    // Set up RTL or LTR direction
    setupLanguageDirection() {
      const isRtl = this.rtlLanguages.includes(this.currentLanguage);
      document.documentElement.dir = isRtl ? 'rtl' : 'ltr';
      document.documentElement.lang = this.currentLanguage;
      
      // Add/remove RTL class from body
      if (isRtl) {
        document.body.classList.add('rtl');
      } else {
        document.body.classList.remove('rtl');
      }
      
      // Update any directional CSS variables if needed
      document.documentElement.style.setProperty('--text-direction', isRtl ? 'rtl' : 'ltr');
    }
    
    // Get translation for a key
    translate(key) {
      // Split key by dots to access nested properties
      const parts = key.split('.');
      let translation = this.translations[this.currentLanguage];
      
      // Navigate through nested structure
      for (const part of parts) {
        if (!translation || typeof translation !== 'object') {
          return key; // Key not found
        }
        translation = translation[part];
      }
      
      if (translation === undefined || translation === null) {
        // Try with default language as fallback
        let defaultTranslation = this.translations[this.defaultLanguage];
        if (defaultTranslation) {
          for (const part of parts) {
            if (!defaultTranslation || typeof defaultTranslation !== 'object') {
              return key;
            }
            defaultTranslation = defaultTranslation[part];
          }
          return defaultTranslation || key;
        }
        return key;
      }
      
      return translation;
    }
    
    // Translate all elements with data-i18n attribute
    translatePage() {
      if (!this.translations[this.currentLanguage]) {
        console.warn(`Translations for ${this.currentLanguage} not loaded yet`);
        return;
      }
      
      // Elements with data-i18n attribute
      const elements = document.querySelectorAll('[data-i18n]');
      elements.forEach(element => {
        const key = element.getAttribute('data-i18n');
        const translation = this.translate(key);
        if (translation !== key) {
          element.textContent = translation;
        }
      });
      
      // Elements with data-i18n-placeholder
      const placeholders = document.querySelectorAll('[data-i18n-placeholder]');
      placeholders.forEach(element => {
        const key = element.getAttribute('data-i18n-placeholder');
        const translation = this.translate(key);
        if (translation !== key) {
          element.placeholder = translation;
        }
      });
      
      // Elements with data-i18n-html
      const htmlElements = document.querySelectorAll('[data-i18n-html]');
      htmlElements.forEach(element => {
        const key = element.getAttribute('data-i18n-html');
        const translation = this.translate(key);
        if (translation !== key) {
          element.innerHTML = translation;
        }
      });
      
      // Elements with data-i18n-title
      const titleElements = document.querySelectorAll('[data-i18n-title]');
      titleElements.forEach(element => {
        const key = element.getAttribute('data-i18n-title');
        const translation = this.translate(key);
        if (translation !== key) {
          element.title = translation;
        }
      });
    }
  }
  
  // Create global instance
  const translationManager = new TranslationManager();
  
  // Global shorthand function for translations
  function _t(key) {
    return translationManager.translate(key);
  }
  
  // Export for use in other scripts if using modules
  if (typeof module !== 'undefined' && module.exports) {
    module.exports = { translationManager, _t };
  }
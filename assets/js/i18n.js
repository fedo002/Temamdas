
  /**
 * i18n.js - Çoklu dil desteği için JavaScript kütüphanesi
 * 
 * Bu kütüphane, web sitesinin çoklu dil desteği için gerekli fonksiyonları içerir.
 * data-i18n ve data-i18n-html öznitelikleri kullanılarak etiketlenmiş metinlerin
 * çevirilerini yapar.
 */

class I18nManager {
    constructor() {
        this.translations = {};
        this.currentLanguage = 'tr';
        this.defaultLanguage = 'tr';
        this.isLoading = false;
        
        // Dil değiştiricileri için event listener
        document.addEventListener('DOMContentLoaded', () => {
            console.log('I18nManager: DOM yüklendi, dil değiştiriciler başlatılıyor...');
            const languageLinks = document.querySelectorAll('[data-language]');
            console.log(`I18nManager: ${languageLinks.length} dil değiştirici bulundu`);
            
            languageLinks.forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const lang = link.getAttribute('data-language');
                    console.log(`I18nManager: Dil değiştiriliyor: ${lang}`);
                    this.changeLanguage(lang);
                    
                    // Diğer dil butonlarının aktif sınıfını kaldır
                    languageLinks.forEach(btn => btn.parentElement.classList.remove('active'));
                    // Seçilen dil butonuna aktif sınıfı ekle
                    link.parentElement.classList.add('active');
                });
            });
            
            // Başlangıçta tarayıcı dilini kontrol et veya localStorage'dan yükle
            this.initLanguage();
        });
    }
    
/**
 * İlk dil seçimini belirle - localStorage veya tarayıcı dili
 */
initLanguage() {
    console.log('I18nManager: Dil başlatılıyor...');
    // localStorage'dan dil ayarını kontrol et
    const savedLang = localStorage.getItem('selectedLanguage');
    console.log(`I18nManager: Kaydedilmiş dil: ${savedLang}`);
    
    if (savedLang && this.isLanguageSupported(savedLang)) {
        this.currentLanguage = savedLang;
        console.log(`I18nManager: localStorage'dan yüklenen dil: ${this.currentLanguage}`);
    } else {
        // Tarayıcı dilini kullan
        const browserLang = navigator.language || navigator.userLanguage;
        const shortLang = browserLang.split('-')[0];
        console.log(`I18nManager: Tarayıcı dili: ${browserLang}, Kısa versiyon: ${shortLang}`);
        
        if (this.isLanguageSupported(shortLang)) {
            this.currentLanguage = shortLang;
        } else {
            this.currentLanguage = this.defaultLanguage;
        }
        
        // Seçilen dili localStorage'a kaydet
        localStorage.setItem('selectedLanguage', this.currentLanguage);
        console.log(`I18nManager: Yeni dil localStorage'a kaydedildi: ${this.currentLanguage}`);
    }
    
    console.log(`I18nManager: Aktif dil: ${this.currentLanguage}`);
    
    // HTML lang özniteliğini güncelle
    document.documentElement.lang = this.currentLanguage;
    
    // Çevirileri yükle
    this.loadTranslations(this.currentLanguage)
        .then(() => {
            this.updateDOMTranslations();
            this.updateLanguageSelector(this.currentLanguage);
        })
        .catch(error => {
            console.error('I18nManager: Çeviri yükleme hatası:', error);
        });
}
    /**
     * Desteklenen diller listesini kontrol et
     */
    isLanguageSupported(lang) {
        return ['tr', 'en'].includes(lang);
    }
    
    /**
     * Dil dosyasını yükle
     */
    async loadTranslations(lang) {
        console.log(`I18nManager: ${lang} dili için çeviriler yükleniyor...`);
        
        if (this.isLoading) {
            console.log('I18nManager: Zaten çeviriler yükleniyor, işlem atlanıyor...');
            return this.translations;
        }
        
        this.isLoading = true;
        
        try {
            const response = await fetch(`assets/i18n/${lang}.json`);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            this.translations = await response.json();
            console.log(`I18nManager: ${lang} çevirileri başarıyla yüklendi.`);
            this.isLoading = false;
            return this.translations;
        } catch (error) {
            console.error(`I18nManager: Çeviri yüklenemedi (${lang}):`, error);
            
            // Varsayılan dili yüklemeyi dene
            if (lang !== this.defaultLanguage) {
                console.log(`I18nManager: Varsayılan dile (${this.defaultLanguage}) dönülüyor...`);
                this.isLoading = false;
                return this.loadTranslations(this.defaultLanguage);
            }
            
            this.isLoading = false;
            return {};
        }
    }
    
    /**
     * Dili değiştir
     */
    changeLanguage(lang) {
        if (!this.isLanguageSupported(lang)) {
            console.error(`I18nManager: Dil "${lang}" desteklenmiyor.`);
            return;
        }
        
        if (this.currentLanguage === lang) {
            console.log(`I18nManager: Zaten "${lang}" dilini kullanıyorsunuz.`);
            return;
        }
        
        console.log(`I18nManager: Dil "${this.currentLanguage}" => "${lang}" olarak değiştiriliyor...`);
        
        // Animasyon için sınıf ekle
        document.body.classList.add('language-changing');
        
        this.currentLanguage = lang;
        localStorage.setItem('selectedLanguage', lang);
        
        // HTML lang özniteliğini güncelle
        document.documentElement.lang = lang;
        
        this.loadTranslations(lang)
            .then(() => {
                this.updateDOMTranslations();
                this.updateLanguageSelector(lang);
                
                // Animasyon sonrası sınıfı kaldır
                setTimeout(() => {
                    document.body.classList.remove('language-changing');
                }, 300);
                
                console.log(`I18nManager: Dil başarıyla "${lang}" olarak değiştirildi.`);
            })
            .catch(error => {
                document.body.classList.remove('language-changing');
                console.error('I18nManager: Dil değiştirme hatası:', error);
            });
    }
    
/**
 * Dil seçici UI'ı güncelle
 */
updateLanguageSelector(lang) {
    console.log(`I18nManager: Dil seçici güncelleniyor: ${lang}`);
    
    // Dil seçici butonları güncelle
    const languageLinks = document.querySelectorAll('[data-language]');
    languageLinks.forEach(link => {
        const linkLang = link.getAttribute('data-language');
        if (linkLang === lang) {
            link.parentElement.classList.add('active');
        } else {
            link.parentElement.classList.remove('active');
        }
    });
    
    // Navbar'daki dil göstergesini güncelle
    const languageIndicator = document.querySelector('#languageDropdown');
    if (languageIndicator) {
        languageIndicator.innerHTML = `<i class="fas fa-globe me-1"></i> ${lang.toUpperCase()}`;
        console.log('I18nManager: Dil göstergesi güncellendi');
    } else {
        console.warn('I18nManager: #languageDropdown bulunamadı!');
    }
    
    // localStorage'a dil ayarını kaydet (tekrar kontrol amaçlı)
    localStorage.setItem('selectedLanguage', lang);
    console.log(`I18nManager: Dil ayarı localStorage'a kaydedildi: ${lang}`);
}
    
    /**
     * DOM üzerindeki tüm çevirileri güncelle
     */
    updateDOMTranslations() {
        console.log('I18nManager: DOM çevirileri güncelleniyor...');
        
        // Çeviri sayaçları
        let totalElements = 0;
        let translatedElements = 0;
        
        // Basit metin çevirileri
        const elements = document.querySelectorAll('[data-i18n]');
        totalElements += elements.length;
        
        elements.forEach(el => {
            const key = el.getAttribute('data-i18n');
            const translation = this.getTranslation(key);
            if (translation) {
                el.textContent = translation;
                translatedElements++;
            }
        });
        
        // HTML içeren çeviriler
        const htmlElements = document.querySelectorAll('[data-i18n-html]');
        totalElements += htmlElements.length;
        
        htmlElements.forEach(el => {
            const key = el.getAttribute('data-i18n-html');
            const translation = this.getTranslation(key);
            if (translation) {
                el.innerHTML = translation;
                translatedElements++;
            }
        });
        
        // Placeholder çevirileri
        const placeholderElements = document.querySelectorAll('[data-i18n-placeholder]');
        totalElements += placeholderElements.length;
        
        placeholderElements.forEach(el => {
            const key = el.getAttribute('data-i18n-placeholder');
            const translation = this.getTranslation(key);
            if (translation) {
                el.setAttribute('placeholder', translation);
                translatedElements++;
            }
        });
        
        // Title çevirileri
        const titleElements = document.querySelectorAll('[data-i18n-title]');
        totalElements += titleElements.length;
        
        titleElements.forEach(el => {
            const key = el.getAttribute('data-i18n-title');
            const translation = this.getTranslation(key);
            if (translation) {
                el.setAttribute('title', translation);
                translatedElements++;
            }
        });
        
        console.log(`I18nManager: Çeviri tamamlandı. Toplam ${totalElements} elemandan ${translatedElements} tanesi çevrildi.`);
    }
    
    /**
     * Belirtilen anahtara göre çeviri değerini al
     * Nokta notasyonu kullanılır: "dashboard.total_deposit" gibi
     */
    getTranslation(key) {
        if (!key) return null;
        
        const parts = key.split('.');
        let translation = this.translations;
        
        for (const part of parts) {
            if (translation && typeof translation === 'object' && part in translation) {
                translation = translation[part];
            } else {
                console.warn(`I18nManager: Çeviri anahtarı bulunamadı: ${key}`);
                return null;
            }
        }
        
        return translation;
    }
}

// Çeviri yöneticisini başlat
const i18n = new I18nManager();

// Global olarak kullanılabilir yap
window.i18n = i18n;
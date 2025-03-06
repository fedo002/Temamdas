<?php
/**
 * NowPayments API Integration Class
 * 
 * TRC-20 USDT entegrasyonu için kapsamlı sınıf
 */
class NowPaymentsAPI {
    private $apiKey;
    private $ipnSecret;
    private $testMode;
    private $baseUrl;
    
    /**
     * Sınıf oluşturucusu
     * 
     * @param string $apiKey NOWPayments API anahtarı
     * @param string $ipnSecret NOWPayments IPN secret anahtarı
     * @param bool $testMode Test modu (varsayılan: false)
     */
    public function __construct($apiKey, $ipnSecret = '', $testMode = false) {
        $this->apiKey = $apiKey;
        $this->ipnSecret = $ipnSecret;
        $this->testMode = $testMode;
        $this->baseUrl = $this->testMode ? 'https://api-sandbox.nowpayments.io/v1/' : 'https://api.nowpayments.io/v1/';
    }
    
    /**
     * API'ye istek gönderir
     * 
     * @param string $endpoint API endpoint
     * @param array $data İstek verisi
     * @param string $method HTTP metodu (GET, POST)
     * @return array|bool API cevabı veya hata durumunda false
     */
    private function sendRequest($endpoint, $data = [], $method = 'GET') {
        $url = $this->baseUrl . $endpoint;
        
        $headers = [
            'x-api-key: ' . $this->apiKey,
            'Content-Type: application/json'
        ];
        
        $ch = curl_init();
        
        if ($method === 'GET') {
            if (!empty($data)) {
                $url .= '?' . http_build_query($data);
            }
        } else {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);
        
        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($response, true);
        } else {
            error_log("NowPayments API Error: HTTP Code $httpCode - $response");
            return false;
        }
    }
    
    /**
     * API durumunu kontrol eder
     * 
     * @return bool API erişilebilir mi
     */
    public function checkStatus() {
        $response = $this->sendRequest('status');
        return isset($response['message']) && $response['message'] === 'OK';
    }
    
    /**
     * Mevcut kurları alır
     * 
     * @return array|bool Kurlar veya hata durumunda false
     */
    public function getCurrencies() {
        return $this->sendRequest('currencies');
    }
    
    /**
     * Minimum ödeme miktarını alır
     * 
     * @param string $currency_from Kaynak para birimi
     * @param string $currency_to Hedef para birimi
     * @return float|bool Minimum miktar veya hata durumunda false
     */
    public function getMinAmount($currency_from = 'usd', $currency_to = 'trx') {
        $response = $this->sendRequest('min-amount', [
            'currency_from' => $currency_from,
            'currency_to' => $currency_to
        ]);
        
        return $response ? $response['min_amount'] : false;
    }
    
    /**
     * Fiyat bilgisini alır
     * 
     * @param float $amount Miktar
     * @param string $currency_from Kaynak para birimi
     * @param string $currency_to Hedef para birimi
     * @return array|bool Fiyat bilgisi veya hata durumunda false
     */
    public function getPrice($amount, $currency_from = 'usd', $currency_to = 'trx') {
        return $this->sendRequest('estimate', [
            'amount' => $amount,
            'currency_from' => $currency_from,
            'currency_to' => $currency_to
        ]);
    }
    
    /**
     * Ödeme oluşturur
     * 
     * @param float $price_amount Ücret miktarı
     * @param string $price_currency Ücret para birimi
     * @param string $pay_currency Ödeme para birimi
     * @param string $order_id Sipariş ID
     * @param string $order_description Sipariş açıklaması
     * @param string $ipn_callback_url IPN callback URL
     * @return array|bool Ödeme bilgisi veya hata durumunda false
     */
    public function createPayment($price_amount, $price_currency = 'usd', $pay_currency = 'usdttrc20', $order_id = '', $order_description = '', $ipn_callback_url = '') {
        $data = [
            'price_amount' => $price_amount,
            'price_currency' => $price_currency,
            'pay_currency' => $pay_currency
        ];
        
        if (!empty($order_id)) {
            $data['order_id'] = $order_id;
        }
        
        if (!empty($order_description)) {
            $data['order_description'] = $order_description;
        }
        
        if (!empty($ipn_callback_url)) {
            $data['ipn_callback_url'] = $ipn_callback_url;
        }
        
        return $this->sendRequest('payment', $data, 'POST');
    }
    
    /**
     * Fatura oluşturur
     * 
     * @param float $price_amount Ücret miktarı
     * @param string $price_currency Ücret para birimi
     * @param string $order_id Sipariş ID
     * @param string $order_description Sipariş açıklaması
     * @param string $success_url Başarılı ödeme URL
     * @param string $cancel_url İptal edilen ödeme URL
     * @return array|bool Fatura bilgisi veya hata durumunda false
     */
    public function createInvoice($price_amount, $price_currency = 'usd', $order_id = '', $order_description = '', $success_url = '', $cancel_url = '') {
        $data = [
            'price_amount' => $price_amount,
            'price_currency' => $price_currency
        ];
        
        if (!empty($order_id)) {
            $data['order_id'] = $order_id;
        }
        
        if (!empty($order_description)) {
            $data['order_description'] = $order_description;
        }
        
        if (!empty($success_url)) {
            $data['success_url'] = $success_url;
        }
        
        if (!empty($cancel_url)) {
            $data['cancel_url'] = $cancel_url;
        }
        
        return $this->sendRequest('invoice', $data, 'POST');
    }
    
    /**
     * Ödeme durumunu kontrol eder
     * 
     * @param string $paymentId Ödeme ID
     * @return array|bool Ödeme durumu veya hata durumunda false
     */
    public function getPaymentStatus($paymentId) {
        return $this->sendRequest('payment/' . $paymentId);
    }
    
    /**
     * IPN bildirimini doğrular
     * 
     * @param string $ipnData IPN verisi (POST içeriği)
     * @param string $nowpaymentsSignature NOWPayments imzası (HTTP header)
     * @return bool Doğrulama sonucu
     */
    public function verifyIpn($ipnData, $nowpaymentsSignature) {
        if (empty($this->ipnSecret)) {
            error_log("NowPayments IPN Error: IPN Secret is not set");
            return false;
        }
        
        // Veriyi decode et
        $data = json_decode($ipnData, true);
        if (!$data) {
            error_log("NowPayments IPN Error: Invalid JSON data");
            return false;
        }
        
        // HMAC-SHA512 imzasını oluştur
        $sortedData = json_encode($data, JSON_UNESCAPED_SLASHES);
        $generatedSignature = hash_hmac('sha512', $sortedData, $this->ipnSecret);
        
        // İmzaları karşılaştır
        return hash_equals($nowpaymentsSignature, $generatedSignature);
    }
    
    /**
     * IPN olayını işler
     * 
     * @param string $ipnData IPN verisi (POST içeriği)
     * @param string $nowpaymentsSignature NOWPayments imzası (HTTP header)
     * @return array|bool İşlenen IPN verisi veya hata durumunda false
     */
    public function processIpn($ipnData, $nowpaymentsSignature) {
        // IPN verisi doğrulama
        if (!$this->verifyIpn($ipnData, $nowpaymentsSignature)) {
            return false;
        }
        
        $data = json_decode($ipnData, true);
        
        // Ödeme durumunu kontrol et
        if ($data['payment_status'] === 'confirmed' || $data['payment_status'] === 'finished') {
            // Burada ödeme işleme mantığınızı ekleyebilirsiniz
            return $data;
        }
        
        return $data;
    }
}
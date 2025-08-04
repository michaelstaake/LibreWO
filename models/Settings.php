<?php
require_once 'core/Model.php';

class Settings extends Model {
    protected $table = 'settings';
    
    public function getSetting($key, $default = null) {
        $setting = $this->findOneWhere('setting_key = ?', [$key]);
        return $setting ? $setting['setting_value'] : $default;
    }
    
    public function setSetting($key, $value) {
        $existing = $this->findOneWhere('setting_key = ?', [$key]);
        
        if ($existing) {
            return $this->update($existing['id'], [
                'setting_value' => $value,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            return $this->create([
                'setting_key' => $key,
                'setting_value' => $value,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
    
    public function getAllSettings() {
        $settings = $this->findAll();
        $result = [];
        
        foreach ($settings as $setting) {
            $result[$setting['setting_key']] = $setting['setting_value'];
        }
        
        return $result;
    }
    
    public function getCompanyInfo() {
        return [
            'company_name' => $this->getSetting('company_name', 'Your Company'),
            'company_address' => $this->getSetting('company_address', ''),
            'company_phone' => $this->getSetting('company_phone', ''),
            'company_email' => $this->getSetting('company_email', ''),
            'company_logo' => $this->getSetting('company_logo', ''),
            'work_order_disclaimer' => $this->getSetting('work_order_disclaimer', 'Standard disclaimer text here.')
        ];
    }
    
    public function getCaptchaSettings() {
        return [
            'captcha_provider' => $this->getSetting('captcha_provider', 'off'), // off, turnstile, recaptcha
            'turnstile_site_key' => $this->getSetting('turnstile_site_key', ''),
            'turnstile_secret_key' => $this->getSetting('turnstile_secret_key', ''),
            'recaptcha_site_key' => $this->getSetting('recaptcha_site_key', ''),
            'recaptcha_secret_key' => $this->getSetting('recaptcha_secret_key', '')
        ];
    }
    
    public function updateCompanyInfo($data) {
        foreach ($data as $key => $value) {
            $this->setSetting($key, $value);
        }
        return true;
    }
    
    public function updateCaptchaSettings($data) {
        foreach ($data as $key => $value) {
            $this->setSetting($key, $value);
        }
        return true;
    }
    
    public function updateSecuritySettings($data) {
        foreach ($data as $key => $value) {
            $this->setSetting($key, $value);
        }
        return true;
    }
    
    public function updateFormatSettings($data) {
        foreach ($data as $key => $value) {
            $this->setSetting($key, $value);
        }
        return true;
    }
}

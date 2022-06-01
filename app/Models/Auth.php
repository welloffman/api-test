<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auth extends Model
{
    use HasFactory;

    protected $fillable = ['external_uuid', 'lastname', 'firstname', 'thirdname', 'birthday'];
    protected $hidden = ['uuid', 'expire_date', 'created_at', 'updated_at', 'token', 'status'];

    public function isLive() {
        return $this->isActive() && !$this->isExpired();
    }

    public function isActive() {
        return $this->status == 'active';
    }

    public function isExpired() {
        return strtotime($this->expire_date) < time();
    }

    public function prepare()
    {
        $this->uuid = $this->generateUuid();
        $this->token = $this->generateToken();
        $this->expire_date = $this->makeExpireDate();
        $this->status = 'active';
    }

    public function updateToken() {
        $this->token = $this->generateToken();
        $this->expire_date = $this->makeExpireDate();
        $this->save();
    }

    public function disableToken() {
        $this->status = 'disabled';
        $this->save();
    }

    public function getUserData() {
        return [
            'lastname' => $this->lastname,
            'firstname' => $this->firstname,
            'thirdname' => $this->thirdname,
            'birthday' => $this->birthday,
            'external_uuid' => $this->external_uuid,
        ];
    }

    private function generateUuid() 
    {
        $random = bin2hex( random_bytes(10) );
        $string = md5( uniqid($random, true) );
        $parts = [
            substr($string, 0, 8),
            substr($string, 8, 4),
            substr($string, 12, 4),
            substr($string, 16, 4),
            substr($string, 20, 12)
        ];

        return implode('-', $parts);
    }

    private function generateToken() 
    {
        $random = bin2hex( random_bytes(50) );
        return str_replace('.', '', uniqid($random, true));
    }

    private function makeExpireDate() 
    {
        $token_expired_days = config('app.token_expired_days', 30);
        $date = new \DateTime();
        $date->add(new \DateInterval("P{$token_expired_days}D"));
        return $date->format('Y-m-d H:i:s');
    }
}

<?php

namespace App\Models;

use PHPFramework\Model;

class User extends Model {
    protected array $fillable = ['name', 'email', 'password', 'repassword'];

    protected function tableName() : string
    {
        return 'users';
    }

    protected function primaryKeyName(): string
    {
        return 'id';
    }

    public function auth() : bool
    {
        if (!$user = db()->query("SELECT * FROM {$this->tableName()} WHERE email = ?", [$this->email])->getOne()) {
            return false;
        }

        if (!password_verify($this->password, $user['password'])) {
            return false;
        }

        $sessionData = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email']
        ];

        session()->set('user', $sessionData);

        return true;
    }
}
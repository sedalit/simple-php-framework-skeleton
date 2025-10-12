<?php

namespace App\Models;

use PHPFramework\Model;

class Post extends Model {
    protected array $fillable = ['title', 'content', 'slug', 'thumbnail'];

    protected function tableName() : string
    {
        return 'posts';
    }

    protected function primaryKeyName(): string
    {
        return 'id';
    }
}
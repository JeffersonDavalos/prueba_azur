<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class status extends Model
{
    use HasFactory;

    protected $table = 'statuses';

    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'created_at',
        'updated_at'
    ];}

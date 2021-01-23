<?php


namespace Vinlon\Laravel\Options;


use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $key
 * @property string $value
 */
class Option extends Model
{
    protected $fillable = ['key', 'value'];
}

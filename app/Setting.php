<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'value'];

    public $timestamps = false;


    public static function get($name, $default = '') {
        $row = self::where('name', $name)->get()->first();
        return $row == null ? $default : $row->value;
    }

    public static function set($name, $value) {
        if( self::where('name', $name)->count() == 0 ) {
            self::create([
                'name' => $name,
                'value' => $value
            ]);
        } else {
            self::where('name', $name)->update([
               'value' => $value
            ]);
        }
    }
}

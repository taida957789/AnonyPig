<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Mockery\CountValidator\Exception;

class Post extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'posts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['content', 'token', 'post_id'];

    public $timestamps = true;


    private static function generateToken($id)
    {
        $token = str_random(count($id));

        while(self::where('token', $token)->get()->first() != null)
            $token = str_random(count($id));

        return $token;
    }

    public static function addPost($conent) {

        $post = new Post();
        $post->content = $conent;
        $post->token = '';
        $post->post_id = 0;
        $post->save();
        $post->token = self::generateToken($post->id);
        $post->save();

        return $post;
    }

    public static function updatePostId($id, $post_id) {
        $post = self::where('id', $id)->get()->first();
        if( $post != null) {
            $post->post_id = $post_id;
            return $post->save();
        } else {
            return false;
        }
    }
}

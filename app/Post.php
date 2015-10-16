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
    protected $fillable = ['content', 'job_id', 'post_id', 'ip'];

    public $timestamps = true;


    public static function addPost($conent, $ip);
    {
        $post = new Post();
        $post->ip = $ip;
        $post->content = $conent;
        $post->generateToken();
        $post->save();
        return $post;
    }

    public static function getPostByToken($token)
    {
        return self::where('token', $token)->get()->first();
    }

    public static function updatePostId($id, $post_id)
    {
        $post = self::where('id', $id)->get()->first();
        if( $post != null) {
            $post->post_id = $post_id;
            return $post->save();
        } else {
            return false;
        }
    }

    public static function updateJobId($id, $job_id) {
        $post = self::where('id', $id)->get()->first();
        if( $post != null) {
            $post->job_id = $job_id;
            return $post->save();
        } else {
            return false;
        }
    }

    public function generateToken()
    {
        $token = str_random(max([count("$this->id"),4]));
        while( self::where('token', $token)->count() != 0)
            $token = str_random(max(count($this->id, 4)));
        $this->token = $token;
    }
}

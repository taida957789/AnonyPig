<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Post;
use App\Setting;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\App;
use PhpParser\Node\Scalar\MagicConst\File;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk;

class PublishPost extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $post;
    protected $token;

    /**
     * Create a new job instance.
     *
     * @param Post $post
     * @param $pageToken
     */
    public function __construct(Post $post, $pageToken)
    {
        $this->post = $post;
        $this->token = $pageToken;
    }

    /**
     * Execute the job.
     *
     * @param LaravelFacebookSdk $fb
     */
    public function handle(LaravelFacebookSdk $fb)
    {

        //$fb = App::make('SammyK\LaravelFacebookSdk\LaravelFacebookSdk');
        $fb->setDefaultAccessToken($this->token);

        $pageId = Setting::get('page_id');

        // format the publish content ( hashtag .. or some information )
        $hashTag = Setting::get('hash_tag');


        $link = '';
        $publishContent = '#'.$hashTag.'_'.$this->post->id."\n\n".$this->post->content;
        $count = preg_match("/https?:\\/\\/.\\.imgur\\.com\\/[^ ]*".PHP_EOL."?/", $this->post->content, $matches);

        if($count > 0) {
            $link = $matches[0];
            $publishContent = preg_replace("/https?:\\/\\/.\\.imgur\\.com\\/[^ ]*".PHP_EOL."?/", '', $publishContent, 1);
        }



        // publish it
        // ##### We will use async task in the future #####
        $res = $fb->post('/'.$pageId.'/feed', [
            'message' => $publishContent,
            'link' => $link == '' ? null : $link,
        ]);

        $decodeBody = $res->getDecodedBody();

        // update the post_id in database
        Post::updatePostId($this->post->id, $decodeBody['id']);

    }
}

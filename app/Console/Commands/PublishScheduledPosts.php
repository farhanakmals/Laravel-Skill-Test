<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;

class PublishScheduledPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posts:publish-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish scheduled posts whose publish time has arrived';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $posts = Post::where('is_draft', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->get();

        foreach ($posts as $post) {
            $post->update(['is_draft' => false]);
        }

        $this->info("Published {$posts->count()} scheduled posts.");
    }
}

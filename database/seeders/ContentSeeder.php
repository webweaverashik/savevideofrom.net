<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Platform;
use App\Models\SupportedSite;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        // Popular-website card copy (original wording).
        $cards = [
            'youtube'     => 'Save YouTube videos, music, tutorials, and shorts for offline viewing — no software or extensions. Choose any quality up to 4K, or pull just the MP3 audio.',
            'facebook'    => 'Download Facebook videos and Reels in HD straight to your phone, tablet, or desktop. Copy the post link, paste it in, and save in seconds.',
            'instagram'   => 'Save Instagram Reels, videos, and posts in their original quality — no screen recording and no extra apps required.',
            'tiktok'      => 'Download TikTok videos quickly and reliably. Trends, tutorials, and clips saved straight to your device, clean and watermark-free where available.',
            'twitter'     => 'Save videos from X (Twitter) in high quality directly through your browser, with no app to install.',
            'reddit'      => 'Download Reddit videos in your choice of quality for offline viewing anytime, audio included.',
            'vimeo'       => 'Save high-quality Vimeo videos without ads or pop-ups, ready for smooth offline playback.',
            'dailymotion' => 'Download Dailymotion videos across entertainment, sports, and music in just a few seconds.',
            'linkedin'    => 'Save LinkedIn webinars, talks, and professional videos for offline learning and reference.',
            'threads'     => 'Download Threads videos and short-form clips in their original quality, instantly.',
            'pinterest'   => 'Save Pinterest idea videos, tutorials, and creative clips directly to your device.',
        ];

        foreach ($cards as $slug => $desc) {
            Platform::where('slug', $slug)->update(['card_description' => $desc]);
        }

        // The long tail — "15,000+ sites" grid.
        $sites = [
            ['9GAG', 'Save trending and funny 9GAG clips for offline laughs anytime.'],
            ['Bilibili', 'Download anime, gaming, and creator videos from Bilibili in HD.'],
            ['BitChute', 'Save BitChute videos straight to your device for offline viewing.'],
            ['Blogger', 'Pull embedded Blogger videos by pasting the post URL.'],
            ['Bluesky', 'Download high-quality videos shared on Bluesky in a click.'],
            ['BuzzFeed', 'Keep BuzzFeed clips and explainers to watch later.'],
            ['CapCut', 'Save CapCut videos and templates in their original quality.'],
            ['Chingari', 'Grab Chingari short videos without watermarks.'],
            ['Douyin', 'Download trending Douyin videos in crisp HD.'],
            ['ESPN', 'Save sports highlights, interviews, and clips from ESPN.'],
            ['Flickr', 'Download Flickr videos and creative media to your device.'],
            ['iFunny', 'Save viral iFunny clips with fast, reliable downloads.'],
            ['IMDb', 'Grab trailers and entertainment clips from IMDb instantly.'],
            ['Imgur', 'Save Imgur GIFs and video clips in high quality.'],
            ['Kwai', 'Download Kwai videos to your phone or computer in seconds.'],
            ['Lemon8', 'Save Lemon8 lifestyle clips in their original quality.'],
            ['Likee', 'Download Likee videos without losing resolution.'],
            ['Loom', 'Save Loom recordings and presentations for offline sharing.'],
            ['Mashable', 'Keep Mashable tech and trending clips for later.'],
            ['Medal', 'Save gaming highlights and gameplay clips from Medal in HD.'],
            ['Mixcloud', 'Download Mixcloud sets and clips for offline listening.'],
            ['Moj', 'Save Moj short videos with smooth offline playback.'],
            ['OK.ru', 'Download OK.ru videos with multiple quality options.'],
            ['Rumble', 'Save Rumble videos quickly and safely to your device.'],
            ['ShareChat', 'Download ShareChat clips and trending videos in a tap.'],
            ['Snapchat', 'Save Snapchat Spotlight videos and stories in HD.'],
            ['SoundCloud', 'Download SoundCloud audio and clips for offline play.'],
            ['Streamable', 'Save Streamable videos instantly with no buffering.'],
            ['Substack', 'Download video content shared in Substack posts.'],
            ['TED', 'Save inspiring TED Talks for offline learning.'],
            ['Telegram', 'Download Telegram videos and shared media to your device.'],
            ['Tumblr', 'Save Tumblr videos and GIFs without extra extensions.'],
            ['Twitch', 'Download Twitch clips, streams, and gaming highlights.'],
            ['VK', 'Save VK videos in high quality from your browser.'],
            ['Xiaohongshu', 'Download Xiaohongshu lifestyle and creator videos.'],
        ];

        foreach ($sites as $i => [$name, $desc]) {
            SupportedSite::updateOrCreate(
                ['name' => $name],
                ['description' => $desc, 'is_active' => true, 'sort_order' => $i],
            );
        }
    }
}
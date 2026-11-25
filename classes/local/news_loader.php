<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Loads the configured news items, shared by the block and the "all news" page.
 *
 * @package     block_newsslider
 * @author      BORNET Stéphen.
 * @copyright   2023 YMAG info@ymag.fr https://www.ymag.fr/
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_newsslider\local;

use context;
use context_system;

/**
 * Class news_loader
 *
 * The single place where the news items are read from the plugin configuration. The block
 * and the "all news" page both go through it, so the targeting filter can never be applied
 * to one and forgotten on the other — a targeted news item would otherwise be readable by
 * anyone who knows the page URL.
 */
class news_loader {

    /**
     * The named colours a news tag can take, one per news item ("newstagcolourXX" setting).
     *
     * They are the named colours of the Neo design system Tag component. The single list:
     * lib.php builds the setting choices from it, get_items() validates the stored value
     * against it, and styles.css carries one ".nsl-tag--<name>" rule per entry.
     */
    public const TAG_COLOURS = [
        'blue', 'cyan', 'green', 'yellow', 'pink', 'red', 'orange', 'teal', 'purple', 'indigo', 'neutral',
    ];

    /**
     * The extensions the news image file managers accept (SAST L-069010).
     *
     * An explicit list of raster formats, never the "image" group, which includes SVG — a
     * document that can carry scripts. The single list: lib.php and the file manager setting
     * pass it as "accepted_types".
     */
    public const IMAGE_EXTENSIONS = ['.jpg', '.jpeg', '.png', '.gif', '.webp'];

    /**
     * The MIME types a news image may have to be kept, listed and served inline.
     *
     * The server-side counterpart of IMAGE_EXTENSIONS : the core does not apply "accepted_types"
     * when it saves a draft area, so is_servable_image() is what actually filters.
     */
    public const IMAGE_MIMETYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    /** @var array Custom profile field values per user id, loaded once per request. */
    private static array $profilevalues = [];

    /**
     * Returns the news items visible to the current user.
     *
     * @param  bool  $withmedia Whether to load images and video; false leaves the file storage untouched.
     * @return array ['items' => array of news arrays, 'has_media' => bool].
     */
    public static function get_items(bool $withmedia): array {
        $context = context_system::instance();

        $newsitems = [];
        $has_media = false;

        for ($i = 1; $i <= 6; $i++) {
            $idx = str_pad($i, 2, '0', STR_PAD_LEFT); // '01' … '06'

            // Untitled or targeted at someone else : the same test guards the news images.
            if (!self::is_news_visible($i)) {
                continue;
            }

            // ── Images & video ───────────────────────────────────────────
            // The list mode displays neither, so the file storage is not queried at all.
            $images    = $withmedia ? self::get_news_images($context, $idx) : [];
            $has_imgs  = !empty($images);
            $videodata = $withmedia
                ? self::parse_video_url((string)get_config('block_newsslider', 'newsvideourl' . $idx))
                : self::parse_video_url('');

            // ── Track whether ANY item has media ─────────────────────────
            if ($has_imgs || !empty($videodata['has_video'])) {
                $has_media = true;
            }

            // ── Build news array ─────────────────────────────────────────
            // The link is a free text setting placed in a href: "javascript:" URLs are dropped.
            $url         = self::clean_url((string)get_config('block_newsslider', 'newsurl' . $idx));
            $target      = (string)get_config('block_newsslider', 'newstarget' . $idx);
            $rawtitle    = (string)get_config('block_newsslider', 'newstitle' . $idx);
            $rawcontent  = (string)get_config('block_newsslider', 'newscontent' . $idx);
            $rawheadline = (string)get_config('block_newsslider', 'newsheadline' . $idx);
            $datetext    = self::format_news_date((string)get_config('block_newsslider', 'newsdate' . $idx));

            // Optional named tag colour. Anything outside the list (unset, or a value written
            // by other means than the settings page) falls back to the block colour: the
            // name lands in a class attribute, so it must never be free text.
            $tagcolour = (string)get_config('block_newsslider', 'newstagcolour' . $idx);
            if (!in_array($tagcolour, self::TAG_COLOURS, true)) {
                $tagcolour = '';
            }

            // List mode : with no headline of its own, the content moves up to play the
            // headline and no excerpt is rendered. This is what keeps already configured
            // sites readable the moment the list mode is switched on, without any re-typing.
            $hasheadline = trim($rawheadline) !== '';
            if (!$hasheadline) {
                $rawheadline = $rawcontent;
            }

            $news = [
                'class'   => $i === 1 ? 'glideAppear' : 'glideDisappear',
                'title'   => self::format_news_text($rawtitle, $context),
                'content' => self::format_news_text($rawcontent, $context),
                'link'    => $url === '' ? '#' : $url,
                'haslink' => $url !== '',
                'target'  => $target,
                'color'   => get_config('block_newsslider', 'colour'),

                // Named tag colour : the templates emit a "nsl-tag--<name>" class instead of
                // the inline block colour, so that a theme can dress each colour.
                'tagcolour'    => $tagcolour,
                'hastagcolour' => $tagcolour !== '',

                // List mode fields.
                'headline'    => self::format_news_text($rawheadline, $context),
                'datetext'    => $datetext,
                'hasdate'     => $datetext !== '',
                'hascategory' => trim($rawtitle) !== '',
                'hasmeta'     => $datetext !== '' || trim($rawtitle) !== '',
                'hasexcerpt'  => $hasheadline && trim($rawcontent) !== '',
                'isblank'     => $target === '_blank',

                // "All news" page : a headline typed for the news itself, not the fallback.
                'hasheadline' => $hasheadline,

                // Media fields.
                'has_media'            => $has_imgs || !empty($videodata['has_video']),
                'has_images'           => $has_imgs,
                'has_multiple_images'  => count($images) > 1,
                'images'               => $images,
            ];

            // Merge video data into the news array.
            $news = array_merge($news, $videodata);

            array_push($newsitems, $news);
        }

        // Correct the first visible item's class after potential filtering.
        if (!empty($newsitems)) {
            $newsitems[0]['class'] = 'glideAppear';
            for ($j = 1; $j < count($newsitems); $j++) {
                $newsitems[$j]['class'] = 'glideDisappear';
            }
        }

        return ['items' => $newsitems, 'has_media' => $has_media];
    }

    /**
     * Whether news item $i is shown to the current user : it has a title, and its targeting
     * filter, if any, accepts the user.
     *
     * The single place where the targeting is evaluated. get_items() calls it for the block and
     * the "all news" page, block_newsslider_pluginfile() for the news images (SAST L-069012) :
     * a targeted news item must not be readable through any of the three.
     *
     * @param  int  $i The 1-based news index (1 … 6).
     * @return bool
     */
    public static function is_news_visible(int $i): bool {
        $idx = str_pad((string)$i, 2, '0', STR_PAD_LEFT);

        // Skip news with no title (false = never configured, '' = explicitly empty).
        if (empty(get_config('block_newsslider', 'newstitle' . $idx))) {
            return false;
        }

        $filterraw = get_config('block_newsslider', 'newsfilter' . $idx);
        if (empty($filterraw)) {
            return true;
        }
        $filterdata = @json_decode($filterraw, true);
        if (empty($filterdata['enabled']) || empty($filterdata['field']) || empty($filterdata['values'])) {
            return true;
        }

        // Admins bypass the filter.
        if (is_siteadmin()) {
            return true;
        }

        $allowedvalues = array_map('strval', (array)$filterdata['values']);
        $uservalues    = self::get_user_profile_values();
        $uservalue     = (string)($uservalues[(string)$filterdata['field']] ?? '');

        // Strict comparison (SAST L-069011) : both sides are strings, and a loose one would let
        // numeric strings such as "1e1" and "10" match.
        return in_array($uservalue, $allowedvalues, true);
    }

    /**
     * Whether a stored news image may be listed and served inline : a raster image only.
     *
     * @param  \stored_file $file
     * @return bool
     */
    public static function is_servable_image(\stored_file $file): bool {
        return in_array($file->get_mimetype(), self::IMAGE_MIMETYPES, true);
    }

    /**
     * The custom profile field values of the current user, keyed by shortname.
     *
     * Read through profile_user_record() and NOT profile_load_data() : the latter loads the
     * form value of each field, which for a menu field is the INDEX of the chosen option
     * (profile_field_menu::edit_load_user_data(), "datakey"), not its text — a menu filter
     * compared that index with the option texts and never matched. profile_user_record() gives
     * the stored data, the option text, which is what the targeting widget offers.
     *
     * @return array
     */
    private static function get_user_profile_values(): array {
        global $CFG, $USER;

        $userid = (int)($USER->id ?? 0);
        if (!array_key_exists($userid, self::$profilevalues)) {
            $values = [];
            if ($userid > 0) {
                require_once($CFG->dirroot . '/user/profile/lib.php');
                $values = (array)profile_user_record($userid, false);
            }
            self::$profilevalues[$userid] = $values;
        }
        return self::$profilevalues[$userid];
    }

    /**
     * Formats a configured news string for output.
     *
     * @param  string  $text    The raw value read from the plugin configuration.
     * @param  context $context The system context the filters run against.
     * @return string  The formatted HTML.
     */
    public static function format_news_text(string $text, context $context): string {
        return format_text(
            $text,
            FORMAT_HTML,
            ['noclean' => false, 'filter' => true, 'context' => $context]
        );
    }

    /**
     * Cleans a configured URL before it is placed in a href or a src.
     *
     * PARAM_URL only accepts http, https, ftp, mailto and rtmp schemes (or none),
     * so "javascript:" and "data:" URLs are dropped, as are quotes that would break
     * out of the attribute. It also rejects spaces and accented characters, which are
     * legitimate in a file name: they are percent-encoded first so that such URLs are kept.
     *
     * @param  string $url       The URL as stored in the settings.
     * @param  bool   $httpsonly True to require an https:// URL (iframes).
     * @return string The URL, or '' if it is not acceptable.
     */
    public static function clean_url(string $url, bool $httpsonly = false): string {
        $url = preg_replace_callback('/[^\x21-\x7E]/', function ($m) {
            return rawurlencode($m[0]);
        }, trim($url));
        $url = clean_param($url, PARAM_URL);
        if ($httpsonly && stripos($url, 'https://') !== 0) {
            return '';
        }
        return $url;
    }

    /**
     * Turns a stored "YYYY-MM-DD" date into a short localised label such as "18 août".
     *
     * Three details matter here. The 'strftimedateshort' format is '%d %B', which reads
     * "18 août" in French and "18 August" in English. userdate() strips the leading zero
     * of the day through its $fixday argument, which defaults to true — the wanted
     * behaviour for once. And the timestamp is built at midday, so that rendering it in
     * the reader's own timezone can never shift it to the previous or the next day.
     *
     * @param  string $date The stored value, "YYYY-MM-DD" or empty.
     * @return string The localised date, or '' when unset or unparsable.
     */
    private static function format_news_date(string $date): string {
        if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', trim($date), $matches)) {
            return '';
        }

        [, $year, $month, $day] = array_map('intval', $matches);

        if (!checkdate($month, $day, $year)) {
            return '';
        }

        return userdate(
            make_timestamp($year, $month, $day, 12, 0, 0),
            get_string('strftimedateshort', 'langconfig')
        );
    }

    /**
     * Returns an array of image data arrays for the given news index.
     *
     * Each entry: ['url' => '…', 'alt' => ''].
     *
     * @param  context $context System context.
     * @param  string  $idx     Zero-padded news index ('01' … '06').
     * @return array
     */
    private static function get_news_images(context $context, string $idx): array {
        $fs       = get_file_storage();
        $filearea = 'newsimage' . $idx;
        $files    = $fs->get_area_files(
            $context->id,
            'block_newsslider',
            $filearea,
            0,
            'filename',
            false
        );

        $images = [];
        foreach ($files as $file) {
            // A file outside the raster list (an SVG stored before 0.6.1) is never listed.
            if (!self::is_servable_image($file)) {
                continue;
            }
            $url      = \moodle_url::make_pluginfile_url(
                $context->id,
                'block_newsslider',
                $filearea,
                0,
                $file->get_filepath(),
                $file->get_filename()
            );
            $images[] = ['url' => $url->out(false), 'alt' => ''];
        }
        return $images;
    }

    /**
     * Parses a raw video URL into a data array suitable for Mustache templates.
     *
     * Supports YouTube, Vimeo, and direct video files (mp4, webm, ogg).
     *
     * @param  string $url Raw video URL entered in the settings.
     * @return array  Keys: has_video, video_is_embed, video_embed_url, video_is_file, video_file_url.
     */
    private static function parse_video_url(string $url): array {
        $base = [
            'has_video'       => false,
            'video_is_embed'  => false,
            'video_embed_url' => '',
            'video_is_file'   => false,
            'video_file_url'  => '',
        ];

        $url = trim($url);
        if (empty($url)) {
            return $base;
        }

        // An embed URL is used as-is, so it must really point to the player: a pattern found
        // anywhere in the string would let "javascript:…//player.vimeo.com/video/" into the iframe.
        $embedurl  = self::clean_url($url, true);
        $embedhost = strtolower((string)parse_url($embedurl, PHP_URL_HOST));
        $embedpath = (string)parse_url($embedurl, PHP_URL_PATH);

        // YouTube embed URL already in embed format — use as-is.
        if (in_array($embedhost, ['youtube.com', 'www.youtube.com', 'www.youtube-nocookie.com'], true)
                && strpos($embedpath, '/embed/') === 0) {
            return array_merge($base, [
                'has_video'       => true,
                'video_is_embed'  => true,
                'video_embed_url' => $embedurl,
            ]);
        }

        // YouTube: standard URL or shortened youtu.be.
        if (preg_match(
            '/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/',
            $url,
            $matches
        )) {
            return array_merge($base, [
                'has_video'       => true,
                'video_is_embed'  => true,
                'video_embed_url' => 'https://www.youtube-nocookie.com/embed/' . $matches[1],
            ]);
        }

        // Vimeo player embed URL already in embed format — use as-is (preserves ?h= param).
        if ($embedhost === 'player.vimeo.com' && strpos($embedpath, '/video/') === 0) {
            return array_merge($base, [
                'has_video'       => true,
                'video_is_embed'  => true,
                'video_embed_url' => $embedurl,
            ]);
        }

        // Vimeo standard URL — extract ID and build embed URL.
        if (preg_match('/vimeo\.com\/(\d+)/', $url, $matches)) {
            return array_merge($base, [
                'has_video'       => true,
                'video_is_embed'  => true,
                'video_embed_url' => 'https://player.vimeo.com/video/' . $matches[1],
            ]);
        }

        // Direct video file.
        $fileurl = self::clean_url($url);
        $ext = strtolower(pathinfo((string)parse_url($fileurl, PHP_URL_PATH), PATHINFO_EXTENSION));
        if ($fileurl !== '' && in_array($ext, ['mp4', 'webm', 'ogg'])) {
            return array_merge($base, [
                'has_video'      => true,
                'video_is_file'  => true,
                'video_file_url' => $fileurl,
            ]);
        }

        return $base;
    }
}

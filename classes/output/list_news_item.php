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
 * Datas used for view page.
 *
 * @package     block_newsslider
 * @author      BORNET Stéphen.
 * @copyright   2023 YMAG info@ymag.fr https://www.ymag.fr/
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_newsslider\output;

use renderable;
use templatable;
use renderer_base;
use stdClass;

/**
 * Class list_news_item
 *
 * This class implements the renderable and templatable interfaces and is used for handling each news.
 */
class list_news_item implements renderable, templatable {
    /** @var string Class of the "li" item. */
    public $class;
    /** @var string Title of the news (category label). */
    public $title;
    /** @var string The content of the news. */
    public $content;
    /** @var string The URL of the news. */
    public $link;
    /** @var string Value used to open in a new tab or not. */
    public $target;
    /** @var string Title color. */
    public $color;

    // ── Media fields ──────────────────────────────────────────────────────

    /** @var bool Whether this item has any media (image or video). */
    public $has_media;
    /** @var bool Whether this item has at least one image. */
    public $has_images;
    /** @var bool Whether this item has more than one image (carousel arrows). */
    public $has_multiple_images;
    /** @var array Array of ['url' => '…', 'alt' => '…'] for each image. */
    public $images;
    /** @var bool Whether this item has a video. */
    public $has_video;
    /** @var bool Whether the video is an iframe embed (YouTube / Vimeo). */
    public $video_is_embed;
    /** @var string Iframe src URL for embed video. */
    public $video_embed_url;
    /** @var bool Whether the video is a direct file (mp4 / webm / ogg). */
    public $video_is_file;
    /** @var string Direct video file URL. */
    public $video_file_url;

    /**
     * Constructor for the list_news_item class.
     */
    public function __construct(
        $class,
        $title,
        $content,
        $link,
        $target,
        $color,
        bool $has_media          = false,
        bool $has_images         = false,
        bool $has_multiple_images = false,
        array $images            = [],
        bool $has_video          = false,
        bool $video_is_embed     = false,
        string $video_embed_url  = '',
        bool $video_is_file      = false,
        string $video_file_url   = ''
    ) {
        $this->class               = $class;
        $this->title               = $title;
        $this->content             = $content;
        $this->link                = $link;
        $this->target              = $target;
        $this->color               = $color;
        $this->has_media           = $has_media;
        $this->has_images          = $has_images;
        $this->has_multiple_images = $has_multiple_images;
        $this->images              = $images;
        $this->has_video           = $has_video;
        $this->video_is_embed      = $video_is_embed;
        $this->video_embed_url     = $video_embed_url;
        $this->video_is_file       = $video_is_file;
        $this->video_file_url      = $video_file_url;
    }

    /**
     * Exports data for use in a template.
     *
     * @param renderer_base $output The renderer base.
     * @return stdClass The datas used in each news.
     */
    public function export_for_template(renderer_base $output) {
        $data                      = new stdClass();
        $data->class               = $this->class;
        $data->title               = $this->title;
        $data->content             = $this->content;
        $data->link                = $this->link;
        $data->target              = $this->target;
        $data->color               = $this->color;
        $data->has_media           = $this->has_media;
        $data->has_images          = $this->has_images;
        $data->has_multiple_images = $this->has_multiple_images;
        $data->images              = $this->images;
        $data->has_video           = $this->has_video;
        $data->video_is_embed      = $this->video_is_embed;
        $data->video_embed_url     = $this->video_embed_url;
        $data->video_is_file       = $this->video_is_file;
        $data->video_file_url      = $this->video_file_url;
        return $data;
    }
}

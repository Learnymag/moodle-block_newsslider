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
 * Class news
 *
 * This class implements the renderable and templatable interfaces and is used for handling all data for news.
 */
class news implements renderable, templatable {
    /**
     * @var string The news label.
     */
    private $news_label;

    /**
     * @var array The list of news items.
     */
    private $list_news;

    /**
     * @var bool Whether at least one news item has media (image or video).
     *           When true, all items should use the expanded (media) layout.
     */
    private $has_media;

    /**
     * @var bool Whether the block renders as a dated list instead of the carousel.
     */
    private $islist;

    /**
     * @var string URL of the page listing every news item, '' when not configured.
     */
    private $allnewsurl;

    /**
     * Constructor for the news class.
     *
     * @param string $news_label The news label.
     * @param array  $list_news  The list of news items.
     * @param bool   $has_media  True if any item has media.
     * @param bool   $islist     True to render the dated list instead of the carousel.
     * @param string $allnewsurl URL of the "all news" page, '' to render no footer link.
     */
    public function __construct(
        string $news_label,
        array $list_news,
        bool $has_media = false,
        bool $islist = false,
        string $allnewsurl = ''
    ) {
        $this->news_label = $news_label;
        $this->list_news  = $list_news;
        $this->has_media  = $has_media;
        $this->islist     = $islist;
        $this->allnewsurl = $allnewsurl;
    }

    /**
     * Exports data for use in a template.
     *
     * @param renderer_base $output The renderer base.
     * @return array The datas to be used in the two templates : list_news & news_label.
     */
    public function export_for_template(renderer_base $output) {
        return [
            'content'     => $this->news_label,
            'newsitems'   => $this->list_news,
            'has_media'   => $this->has_media,
            'islist'      => $this->islist,
            'allnewsurl'  => $this->allnewsurl,
            'hasallnews'  => $this->allnewsurl !== '',
            'allnewstext' => get_string('allnews', 'block_newsslider'),
        ];
    }
}

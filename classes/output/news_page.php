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
 * Datas used for the "all news" page.
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

/**
 * Class news_page
 *
 * Every news item on one page, its media on the left and its text on the right.
 */
class news_page implements renderable, templatable {

    /**
     * @var array The news items, as returned by news_loader::get_items(true).
     */
    private $newsitems;

    /**
     * Constructor for the news_page class.
     *
     * @param array $newsitems The news items, media included.
     */
    public function __construct(array $newsitems) {
        $this->newsitems = $newsitems;
    }

    /**
     * Exports data for use in a template.
     *
     * @param renderer_base $output The renderer base.
     * @return array The datas used by the news_page template.
     */
    public function export_for_template(renderer_base $output) {
        $items = [];
        foreach ($this->newsitems as $news) {
            // Each slideshow dot needs its own accessible label ("Image 2").
            $number = 0;
            foreach ($news['images'] as $key => $image) {
                $number++;
                $news['images'][$key]['dotlabel'] = get_string('gotoslide', 'block_newsslider', $number);
            }
            $news['has_single_image'] = count($news['images']) === 1;
            $items[] = $news;
        }

        return [
            'newsitems' => $items,
            'hasnews'   => !empty($items),
        ];
    }
}

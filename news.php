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
 * "All news" page : every news item visible to the reader, with its media.
 *
 * Linked from the block footer when the "allnewspage" setting is on.
 *
 * URL : /blocks/newsslider/news.php
 *
 * @package     block_newsslider
 * @author      BORNET Stéphen.
 * @copyright   2023 YMAG info@ymag.fr https://www.ymag.fr/
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

// Same requirement as block_newsslider_pluginfile() : the images would not load otherwise.
require_login();

if (!get_config('block_newsslider', 'allnewspage')) {
    throw new moodle_exception('allnewspagedisabled', 'block_newsslider');
}

$context = context_system::instance();
$pageurl = new moodle_url('/blocks/newsslider/news.php');

// The context must be set BEFORE any format_string() : it reads $PAGE->context, and an
// unset one raises a debugging() that this site's debug handler turns into a 500.
$PAGE->set_context($context);

$title = format_string(
    get_config('block_newsslider', 'newslabel') ?: get_string('newsheading', 'block_newsslider'),
    true,
    ['context' => $context]
);

$PAGE->set_url($pageurl);
$PAGE->set_pagelayout('standard');
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->navbar->add($title, $pageurl);

$PAGE->requires->js_call_amd('block_newsslider/news_page', 'init', []);

// Same loader as the block, so the targeting filter applies here too.
$loaded   = \block_newsslider\local\news_loader::get_items(true);
$renderer = $PAGE->get_renderer('block_newsslider');

echo $OUTPUT->header();
echo $renderer->render(new \block_newsslider\output\news_page($loaded['items']));
echo $OUTPUT->footer();

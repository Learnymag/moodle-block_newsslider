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
 * Plugin strings are defined here.
 *
 * @package     block_newsslider
 * @category    string
 * @author      BORNET Stéphen.
 * @copyright   2023 YMAG info@ymag.fr https://www.ymag.fr/
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'News Slider';
$string['greetings'] = 'Welcome to the News Slider editor!';
$string['mandatoryfields'] = '<span class="mandatoryfields">*</span>';
$string['mandatoryfieldsdesc'] = '<span class="mandatoryfields">* : Mandatory fields.</span>';
$string['newscolour'] = 'Customizable color';
$string['newscolourdesc'] = 'Color that concerns the title block, category and navigation points serving as feedback.';
$string['newscontent'] = 'News content';
$string['newscontentdesc'] = 'Description, in a single sentence, without line break, of the news n°';
$string['newscontentdefault'] = 'Content Example';
$string['newscycle'] = 'Cycle time';
$string['newscycledesc'] = 'Display time in milliseconds of an element before permutation (6000 ms = 6 seconds).';
$string['newscycledefault'] = '6000';
$string['newsheading'] = 'News';
$string['newslabel'] = 'Block title';
$string['newslabeldesc'] = 'Label located to the left of the block.';
$string['newslabeldefault'] = 'News';
$string['newstagcolour'] = 'Tag colour';
$string['newstagcolourdesc'] = 'Colour of the tag displaying the news title. Left on the block colour, the tag takes the customizable colour above.';
$string['newstagcolourdefault'] = 'Block colour';
$string['tagcolour_blue'] = 'Blue';
$string['tagcolour_cyan'] = 'Cyan';
$string['tagcolour_green'] = 'Green';
$string['tagcolour_yellow'] = 'Yellow';
$string['tagcolour_pink'] = 'Pink';
$string['tagcolour_red'] = 'Red';
$string['tagcolour_orange'] = 'Orange';
$string['tagcolour_teal'] = 'Teal';
$string['tagcolour_purple'] = 'Purple';
$string['tagcolour_indigo'] = 'Indigo';
$string['tagcolour_neutral'] = 'Neutral';
$string['newstarget'] = 'Link\'s behaviour';
$string['newstargetdesc'] = 'Open in a new tab news n°';
$string['newstitle'] = 'News title';
$string['newstitledesc'] = 'Concerns the category related to news n°';
$string['newstitledefault'] = 'Title Example';
$string['newsurl'] = 'News link';
$string['newsurldesc'] = 'News URL n°';
$string['newsurldefault'] = 'https://www.ymag.fr';

// ── List display mode strings ─────────────────────────────────────────────────
$string['listmode'] = 'List display mode';
$string['listmodedesc'] = 'Display the news as a dated list instead of the carousel. Each entry shows its category, date, headline and excerpt, one below the other. Images and videos are not displayed in this mode, but they are kept and shown again when the carousel comes back.';
$string['allnewspage'] = 'All news page';
$string['allnewspagedesc'] = 'Adds an "All news" link at the bottom of the block, in both display modes. The link opens a page listing every news item with its media (image, slideshow or video) and its full content. Targeted news items stay visible only to the users they are meant for.';
$string['allnewspagedisabled'] = 'The all news page is not enabled on this site.';
$string['readmore'] = 'Read more';
$string['nonews'] = 'There is no news to display.';
$string['prevslide'] = 'Previous image';
$string['nextslide'] = 'Next image';
$string['gotoslide'] = 'Image {$a}';
$string['allnews'] = 'All news';
$string['newsdate'] = 'News date';
$string['newsdatedesc'] = 'Date displayed in list display mode, for news n°';
$string['newsheadline'] = 'News headline';
$string['newsheadlinedesc'] = 'Headline displayed in bold in list display mode, above the content used as an excerpt. If left empty, the content is used as the headline and no excerpt is displayed. News n°';

// ── Media strings ─────────────────────────────────────────────────────────────
$string['newsimages'] = 'News images';
$string['newsimagesdesc'] = 'Upload up to 5 images for this news item. If multiple images are added, a carousel with navigation arrows will be displayed. Accepted formats: JPG, PNG, GIF, WebP.';
$string['newsvideourl'] = 'News video URL';
$string['newsvideourldesc'] = 'URL of a YouTube, Vimeo, or direct video file (mp4, webm, ogg). Leave empty if no video. Note: if both an image and a video are provided, the image takes priority.';

// ── Targeting / user-profile filter strings ───────────────────────────────────
$string['newsfilter'] = 'User targeting filter for news item';
$string['newsfilterdesc'] = 'Restrict this news item to users whose custom profile field value matches a specific set of values.';
$string['newsfilter_enable'] = 'Enable filtering by user profile field';
$string['newsfilter_field'] = 'User profile field';
$string['newsfilter_selectfield'] = '-- Select a field --';
$string['newsfilter_values'] = 'Target values';
$string['newsfilter_noprofilefields'] = 'No custom profile fields are configured in Moodle. Go to Site administration > Users > User profile fields.';
$string['newsfilter_novalues'] = 'No values found in user profiles for this field. The news item will be visible to everyone until at least one user fills in this field.';
$string['newsfilter_usereditable'] = 'Users can edit this field in their own profile, and so give themselves a targeted value: the filter then sorts the news items, it does not restrict them. To make it a restriction, lock the field in Site administration > Users > User profile fields. Locking only freezes the profile edit form: synchronisations and web services keep writing the field.';

// ── Privacy & capabilities ────────────────────────────────────────────────────
$string['privacy:metadata'] = 'The News Slider plugin does not store any personal data about any user.';
$string['myaddinstance'] = 'Allow to add a block News Slider in Dashboard page';
$string['addinstance'] = 'Allow to add a block News Slider';
$string['newsslider:myaddinstance'] = 'Allow to add a block News Slider in Dashboard page';
$string['newsslider:addinstance'] = 'Allow to add a block News Slider';
$string['newsslider:managesettings'] = 'Manage News Slider plugin settings';

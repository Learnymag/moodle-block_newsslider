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
 * Learnymag news block global lib.
 *
 * @package     block_newsslider
 * @author      BORNET Stéphen.
 * @copyright   2023 YMAG info@ymag.fr https://www.ymag.fr/
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Remplit une admin_settingpage avec tous les champs de configuration du plugin.
 * Utilisé à la fois par settings.php (admins) et managesettings.php (managers).
 *
 * @param admin_settingpage $page La page à remplir.
 */
function block_newsslider_populate_settings(admin_settingpage $page) {
    global $PAGE;

    $PAGE->requires->js_call_amd('block_newsslider/settings_amd', 'init', []);

    $page->add(new admin_setting_description(
        'block_newsslider/infosupp', '',
        get_string('greetings', 'block_newsslider')
        . '<br>' . get_string('mandatoryfieldsdesc', 'block_newsslider') . '<br><br>'
    ));

    $page->add(new admin_setting_configcolourpicker(
        'block_newsslider/colour',
        get_string('newscolour', 'block_newsslider'),
        get_string('newscolourdesc', 'block_newsslider'),
        '#28a94d'
    ));

    $page->add(new admin_setting_configtext(
        'block_newsslider/newslabel',
        get_string('newslabel', 'block_newsslider') . ' ' . get_string('mandatoryfields', 'block_newsslider'),
        get_string('newslabeldesc', 'block_newsslider'),
        get_string('newsheading', 'block_newsslider')
    ));

    $page->add(new admin_setting_configtext(
        'block_newsslider/newsclyde',
        get_string('newscycle', 'block_newsslider') . ' ' . get_string('mandatoryfields', 'block_newsslider'),
        get_string('newscycledesc', 'block_newsslider'),
        get_string('newscycledefault', 'block_newsslider')
    ));

    $page->add(new admin_setting_configcheckbox(
        'block_newsslider/listmode',
        get_string('listmode', 'block_newsslider'),
        get_string('listmodedesc', 'block_newsslider'),
        0
    ));

    // Global setting, outside the per-news sections : sectionSelectors() in
    // settings_amd.js does not have to know about it.
    $page->add(new admin_setting_configcheckbox(
        'block_newsslider/allnewspage',
        get_string('allnewspage', 'block_newsslider'),
        get_string('allnewspagedesc', 'block_newsslider'),
        0
    ));

    // Choices of the per-news tag colour : the block colour first, then the named colours.
    $tagcolourchoices = ['' => get_string('newstagcolourdefault', 'block_newsslider')];
    foreach (\block_newsslider\local\news_loader::TAG_COLOURS as $colourname) {
        $tagcolourchoices[$colourname] = get_string('tagcolour_' . $colourname, 'block_newsslider');
    }

    for ($i = 1; $i <= 6; $i++) {
        $idx   = str_pad($i, 2, '0', STR_PAD_LEFT);
        $label = get_string('newsheading', 'block_newsslider') . " $i";

        if ($i === 1) {
            $page->add(new admin_setting_heading("block_newsslider/separatornewsT{$idx}", ' ', $label));
        } else {
            $page->add(new admin_setting_heading(
                "block_newsslider/separatornewsT{$idx}", ' ',
                "<span class='headingsettings' data-sttngstts='wrapped' data-sttngid='{$i}'>"
                . "<i class='bi bi-arrow-right-square-fill text-primary'></i>  " . $label . "</span>"
            ));
        }

        $page->add(new admin_setting_configtext(
            "block_newsslider/newstitle{$idx}",
            get_string('newstitle', 'block_newsslider') . " $i"
                . ($i === 1 ? get_string('mandatoryfields', 'block_newsslider') : ''),
            get_string('newstitledesc', 'block_newsslider') . " $i.",
            $i === 1 ? get_string('newstitledefault', 'block_newsslider') : '',
            PARAM_RAW
        ));

        // Optional per-news tag colour. The empty default keeps the block colour, so a site
        // that never touches it renders exactly as before.
        $page->add(new admin_setting_configselect(
            "block_newsslider/newstagcolour{$idx}",
            get_string('newstagcolour', 'block_newsslider') . " $i",
            get_string('newstagcolourdesc', 'block_newsslider'),
            '',
            $tagcolourchoices
        ));

        $page->add(new \block_newsslider\admin\admin_setting_configdate(
            "block_newsslider/newsdate{$idx}",
            get_string('newsdate', 'block_newsslider') . " $i",
            get_string('newsdatedesc', 'block_newsslider') . " $i.",
            ''
        ));

        $page->add(new admin_setting_configtext(
            "block_newsslider/newsheadline{$idx}",
            get_string('newsheadline', 'block_newsslider') . " $i",
            get_string('newsheadlinedesc', 'block_newsslider') . " $i.",
            '',
            PARAM_RAW
        ));

        $page->add(new admin_setting_configtextarea(
            "block_newsslider/newscontent{$idx}",
            get_string('newscontent', 'block_newsslider') . " $i"
                . ($i === 1 ? get_string('mandatoryfields', 'block_newsslider') : ''),
            get_string('newscontentdesc', 'block_newsslider') . " $i.",
            $i === 1 ? get_string('newscontentdefault', 'block_newsslider') : '',
            PARAM_RAW
        ));

        $page->add(new admin_setting_configtext(
            "block_newsslider/newsurl{$idx}",
            get_string('newsurl', 'block_newsslider') . " $i",
            get_string('newsurldesc', 'block_newsslider') . " $i.",
            get_string('newsurldefault', 'block_newsslider')
        ));

        $page->add(new admin_setting_configcheckbox(
            "block_newsslider/newstarget{$idx}",
            get_string('newstargetdesc', 'block_newsslider') . " $i",
            '', '_self', '_blank', '_self'
        ));

        $page->add(new \block_newsslider\admin\admin_setting_configfilesmanager(
            "block_newsslider/newsimages{$idx}",
            get_string('newsimages', 'block_newsslider') . " $i",
            get_string('newsimagesdesc', 'block_newsslider'),
            "newsimage{$idx}", 0,
            // Raster formats only : the "image" group includes SVG (SAST L-069010).
            [
                'maxfiles'       => 5,
                'accepted_types' => \block_newsslider\local\news_loader::IMAGE_EXTENSIONS,
                'subdirs'        => 0,
            ]
        ));

        $page->add(new admin_setting_configtext(
            "block_newsslider/newsvideourl{$idx}",
            get_string('newsvideourl', 'block_newsslider') . " $i",
            get_string('newsvideourldesc', 'block_newsslider'),
            '', PARAM_RAW
        ));

        $page->add(new \block_newsslider\admin\admin_setting_newstargetfilter(
            "block_newsslider/newsfilter{$idx}",
            get_string('newsfilter', 'block_newsslider') . " $i",
            get_string('newsfilterdesc', 'block_newsslider'),
            $i
        ));
    }

    $page->add(new admin_setting_heading('block_newsslider/separatornewsB06', ' ', ''));
}

/**
 * Serves files from the block_newsslider file areas (news images).
 *
 * File areas follow the pattern "newsimage01", "newsimage02", … "newsimage06".
 *
 * @param stdClass $course   Course object (unused – global settings context).
 * @param stdClass $cm       Course module (unused).
 * @param context  $context  The context must be context_system.
 * @param string   $filearea The filearea name (e.g. 'newsimage01').
 * @param array    $args     Extra path components [ itemid, …, filename ].
 * @param bool     $forcedownload Whether to force a download prompt.
 * @param array    $options  Additional options for send_stored_file().
 * @return bool|void False if the file is not found, otherwise sends the file and exits.
 */
function block_newsslider_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {

    // Only serve files stored under the system context.
    if ($context->contextlevel != CONTEXT_SYSTEM) {
        return false;
    }

    // Accept only our own image file areas: newsimage01 … newsimage06.
    if (!preg_match('/^newsimage0[1-6]$/', $filearea)) {
        return false;
    }

    // Require at least a valid session (non-guest) to view images.
    require_login();

    // The image of a news item the user may not see is not served either (SAST L-069012) :
    // same test as the block and the "all news" page. The regex above bounds the index to 1-6.
    if (!\block_newsslider\local\news_loader::is_news_visible((int)substr($filearea, -2))) {
        return false;
    }

    // Resolve item id, filepath and filename from $args.
    $itemid   = array_shift($args);          // always 0 for global settings files
    $filename = array_pop($args);
    $filepath = $args ? '/' . implode('/', $args) . '/' : '/';

    $fs   = get_file_storage();
    $file = $fs->get_file($context->id, 'block_newsslider', $filearea, $itemid, $filepath, $filename);

    if (!$file) {
        return false;
    }

    // Cache images for 1 day, in the browser only : the answer now depends on the user's
    // targeting, so no shared cache may keep it. Anything but a raster image (an SVG stored
    // before 0.6.1) is sent as a download, never inline under the site origin (SAST L-069010).
    $forcedownload = !\block_newsslider\local\news_loader::is_servable_image($file);
    send_stored_file($file, DAYSECS, 0, $forcedownload, array_merge($options, ['cacheability' => 'private']));
}

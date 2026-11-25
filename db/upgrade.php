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
 * Upgrade steps for block_newsslider.
 *
 * @package     block_newsslider
 * @author      BORNET Stéphen.
 * @copyright   2023 YMAG info@ymag.fr https://www.ymag.fr/
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define upgrade steps to be performed to upgrade the plugin from the old version to the current one.
 *
 * @param int $oldversion Version number the plugin is being upgraded from.
 * @return bool
 */
function xmldb_block_newsslider_upgrade($oldversion) {

    if ($oldversion < 2026092500) {

        // The free "newsallurl" setting gives way to the plugin's own "all news" page.
        // A site that had filled it in wanted a footer link : it keeps one, now pointing
        // to the internal page. A site that had left it empty keeps rendering as before.
        $oldurl = trim((string)get_config('block_newsslider', 'newsallurl'));
        if ($oldurl !== '') {
            set_config('allnewspage', 1, 'block_newsslider');
        }
        unset_config('newsallurl', 'block_newsslider');

        // Savepoint reached. The number MUST match a $plugin->version from version.php.
        upgrade_block_savepoint(true, 2026092500, 'newsslider');
    }

    if ($oldversion < 2026111900) {

        // SAST L-069010 : the news image file managers accepted the "image" group, SVG included.
        // Purge every stored file outside the raster list, so that no SVG is left to serve.
        $fs = get_file_storage();
        $contextid = context_system::instance()->id;
        for ($i = 1; $i <= 6; $i++) {
            $filearea = 'newsimage' . str_pad((string)$i, 2, '0', STR_PAD_LEFT);
            $files = $fs->get_area_files($contextid, 'block_newsslider', $filearea, false, 'id', false);
            foreach ($files as $file) {
                if (!\block_newsslider\local\news_loader::is_servable_image($file)) {
                    $file->delete();
                }
            }
        }

        // Savepoint reached. The number MUST match a $plugin->version from version.php.
        upgrade_block_savepoint(true, 2026111900, 'newsslider');
    }

    return true;
}

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
 * Plugin administration pages are defined here.
 *
 * @package     block_newsslider
 * @category    admin
 * @author      BORNET Stéphen.
 * @copyright   2023 YMAG info@ymag.fr https://www.ymag.fr/
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @thanks      Volodymyr DOVHAN for his help & his patience.
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/newsslider/lib.php');

// Les admins utilisent cette page via /admin/settings.php?section=blocksettingnewsslider.
// Les managers (non-admins) utilisent /blocks/newsslider/managesettings.php.
// Note : load_settings() du framework ne charge ce fichier que si $hassiteconfig = true,
// donc cette page est réservée aux admins ; les managers passent par la page externe.

if ($ADMIN->fulltree) {
    block_newsslider_populate_settings($settings);
}


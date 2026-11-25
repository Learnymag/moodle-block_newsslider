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
 * Custom admin setting : native date picker.
 *
 * @package     block_newsslider
 * @author      BORNET Stéphen.
 * @copyright   2026 YMAG info@ymag.fr https://www.ymag.fr/
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_newsslider\admin;

defined('MOODLE_INTERNAL') || die();

/**
 * Admin setting rendering a native <input type="date"> widget.
 *
 * The value is stored as a plain "YYYY-MM-DD" string rather than a timestamp: it stays
 * readable in the config table, it is fed back into the input as-is, and it cannot drift
 * by one day depending on the reader's timezone. Callers turn it into a timestamp at
 * display time — see block_newsslider::format_news_date().
 */
class admin_setting_configdate extends \admin_setting {

    /**
     * Constructor.
     *
     * @param string $name        Setting name (e.g. 'block_newsslider/newsdate01').
     * @param string $visiblename Human-readable name shown in the settings page.
     * @param string $description Description text shown below the widget.
     * @param string $defaultsetting Default value, "YYYY-MM-DD" or '' for no date.
     */
    public function __construct(string $name, string $visiblename, string $description, string $defaultsetting = '') {
        parent::__construct($name, $visiblename, $description, $defaultsetting);
    }

    /**
     * Returns the stored date.
     *
     * @return string|false The "YYYY-MM-DD" string, '' if unset, false if never configured.
     */
    public function get_setting() {
        return $this->config_read($this->name);
    }

    /**
     * Validates and stores the submitted date.
     *
     * @param  string $data Submitted value, expected as "YYYY-MM-DD" or empty.
     * @return string Empty string on success, error string on failure.
     */
    public function write_setting($data) {
        $data = trim((string)$data);

        // An empty value clears the date: the entry then renders without its date line.
        if ($data !== '' && !self::is_valid_date($data)) {
            return get_string('errorsetting', 'admin');
        }

        return $this->config_write($this->name, $data) ? '' : get_string('errorsetting', 'admin');
    }

    /**
     * Renders the date input for the admin settings page.
     *
     * @param  mixed  $data  The currently stored value.
     * @param  string $query Search query for highlight (not used here).
     * @return string Formatted HTML for the setting row.
     */
    public function output_html($data, $query = '') {
        // A stored value that no longer parses must not reach the widget: the browser would
        // silently drop it and the admin would believe the date was never saved.
        $value = self::is_valid_date((string)$data) ? (string)$data : '';

        $input = \html_writer::empty_tag('input', [
            'type'  => 'date',
            'name'  => $this->get_full_name(),
            'id'    => $this->get_id(),
            'value' => $value,
            'class' => 'form-control text-ltr',
            'size'  => 12,
        ]);

        return format_admin_setting(
            $this,
            $this->visiblename,
            \html_writer::div($input, 'form-text defaultsnext'),
            $this->description,
            true,
            '',
            $this->get_defaultsetting(),
            $query
        );
    }

    /**
     * Tells whether a string is a real calendar date in "YYYY-MM-DD" form.
     *
     * checkdate() is what rejects 2026-02-30, which the regular expression accepts.
     *
     * @param  string $date The value to check.
     * @return bool
     */
    protected static function is_valid_date(string $date): bool {
        if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $matches)) {
            return false;
        }

        return checkdate((int)$matches[2], (int)$matches[3], (int)$matches[1]);
    }
}

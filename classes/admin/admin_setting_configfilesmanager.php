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
 * Custom admin setting : file manager for multiple images.
 *
 * @package     block_newsslider
 * @author      BORNET Stéphen.
 * @copyright   2024 YMAG info@ymag.fr https://www.ymag.fr/
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_newsslider\admin;

defined('MOODLE_INTERNAL') || die();

/**
 * Admin setting that renders a Moodle filemanager widget, allowing multiple image uploads
 * stored in a specific filearea of the block_newsslider component.
 */
class admin_setting_configfilesmanager extends \admin_setting {

    /** @var string The filearea name used to store files permanently. */
    protected string $filearea;

    /** @var int The item id for the filearea (0 for global settings). */
    protected int $itemid;

    /** @var array Options passed to the filemanager widget and file_save_draft_area_files(). */
    protected array $options;

    /**
     * Constructor.
     *
     * @param string $name    Setting name (e.g. 'block_newsslider/newsimages01').
     * @param string $visiblename Human-readable name shown in the settings page.
     * @param string $description Description text shown below the widget.
     * @param string $filearea  Filearea name (e.g. 'newsimage01').
     * @param int    $itemid   Item id (default 0).
     * @param array  $options  Filemanager options (maxfiles, accepted_types, …).
     */
    public function __construct(
        string $name,
        string $visiblename,
        string $description,
        string $filearea,
        int $itemid = 0,
        array $options = []
    ) {
        $this->filearea = $filearea;
        $this->itemid   = $itemid;
        $this->options  = array_merge([
            'maxfiles'       => 5,
            'accepted_types' => \block_newsslider\local\news_loader::IMAGE_EXTENSIONS,
            'subdirs'        => 0,
            'maxbytes'       => 0,
        ], $options);
        parent::__construct($name, $visiblename, $description, '');
    }

    /**
     * Returns the stored draft item id (or empty string if not yet set).
     *
     * @return string|false
     */
    public function get_setting() {
        return $this->config_read($this->name);
    }

    /**
     * Saves the submitted draft item id and moves draft files to permanent storage.
     *
     * @param  string $data Draft item id submitted by the form.
     * @return string Empty string on success, error string on failure.
     */
    public function write_setting($data) {
        global $CFG;

        if (empty($data)) {
            $data = 0;
        }

        if ($this->config_write($this->name, $data)) {
            require_once($CFG->libdir . '/filelib.php');
            $context = \context_system::instance();
            file_save_draft_area_files(
                (int)$data,
                $context->id,
                'block_newsslider',
                $this->filearea,
                $this->itemid,
                $this->options
            );

            // The core does not apply "accepted_types" when it saves a draft area, and the
            // upload repository takes it from the client : this is the server-side check.
            // Anything but a raster image (an SVG above all, SAST L-069010) is dropped.
            $fs = get_file_storage();
            $files = $fs->get_area_files($context->id, 'block_newsslider', $this->filearea, $this->itemid, 'id', false);
            foreach ($files as $file) {
                if (!\block_newsslider\local\news_loader::is_servable_image($file)) {
                    $file->delete();
                }
            }
            return '';
        }

        return get_string('errorsetting', 'admin');
    }

    /**
     * Renders the filemanager widget HTML for the admin settings page.
     *
     * @param  mixed  $data  The currently stored value (draft item id or empty string).
     * @param  string $query Search query for highlight (not used here).
     * @return string Formatted HTML for the setting row.
     */
    public function output_html($data, $query = '') {
        global $CFG;
        require_once($CFG->libdir . '/form/filemanager.php');

        $context     = \context_system::instance();
        $draftitemid = file_get_submitted_draft_itemid($this->get_full_name());

        file_prepare_draft_area(
            $draftitemid,
            $context->id,
            'block_newsslider',
            $this->filearea,
            $this->itemid,
            $this->options
        );

        $fm = new \MoodleQuickForm_filemanager(
            $this->get_full_name(),
            '',
            ['id' => $this->get_id()],
            $this->options
        );
        $fm->setValue($draftitemid);

        $html = $fm->toHtml();

        return format_admin_setting($this, $this->visiblename, $html, $this->description, true, '', '', $query);
    }
}

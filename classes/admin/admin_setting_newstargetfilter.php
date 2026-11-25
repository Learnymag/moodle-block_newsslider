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
 * Custom admin setting : user profile field targeting filter.
 *
 * Stores its configuration as a JSON string:
 *   {"enabled": true, "field": "department", "values": ["IT", "HR"]}
 *
 * @package     block_newsslider
 * @author      BORNET Stéphen.
 * @copyright   2024 YMAG info@ymag.fr https://www.ymag.fr/
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_newsslider\admin;

defined('MOODLE_INTERNAL') || die();

/**
 * Admin setting that renders a composite widget (checkbox + field select + value checkboxes)
 * and serialises the selection to/from JSON stored in the plugin config.
 */
class admin_setting_newstargetfilter extends \admin_setting {

    /** @var int News index (1-6), used to generate unique element ids. */
    protected int $newsindex;

    /**
     * Constructor.
     *
     * @param string $name       Setting name (e.g. 'block_newsslider/newsfilter01').
     * @param string $visiblename Human-readable name.
     * @param string $description Description text.
     * @param int    $newsindex  The 1-based index of the news item this filter applies to.
     */
    public function __construct(string $name, string $visiblename, string $description, int $newsindex) {
        $this->newsindex = $newsindex;
        parent::__construct($name, $visiblename, $description, '');
    }

    /**
     * Returns the stored JSON string (or empty string).
     *
     * @return string|false
     */
    public function get_setting() {
        return $this->config_read($this->name);
    }

    /**
     * Validates and stores the submitted JSON string.
     *
     * @param  string $data JSON string submitted by the form.
     * @return string Empty string on success, error string on failure.
     */
    public function write_setting($data) {
        if ($data === null || $data === false) {
            $data = '';
        }
        $data = trim($data);
        if ($data !== '' && @json_decode($data, true) === null) {
            return get_string('errorsetting', 'admin');
        }
        return $this->config_write($this->name, $data) ? '' : get_string('errorsetting', 'admin');
    }

    /**
     * Renders the targeting filter widget HTML.
     *
     * @param  mixed  $data  Stored JSON string (or empty/false).
     * @param  string $query Search query (unused).
     * @return string Formatted HTML for the setting row.
     */
    public function output_html($data, $query = '') {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/user/profile/lib.php');

        // ── Parse current configuration ──────────────────────────────────
        $current        = [];
        $rawdata        = ($data && $data !== false) ? $data : '';
        if ($rawdata !== '') {
            $current = @json_decode($rawdata, true) ?: [];
        }
        $enabled        = !empty($current['enabled']);
        $selectedfield  = $current['field']  ?? '';
        $selectedvalues = array_map('strval', (array)($current['values'] ?? []));

        // ── Retrieve custom profile fields ───────────────────────────────
        $profilefields = profile_get_custom_fields();

        // Build array of field info suitable for JS data-attribute embedding.
        $fieldsdata  = [];
        $fieldoptions = ['' => get_string('newsfilter_selectfield', 'block_newsslider')];

        foreach ($profilefields as $field) {
            $values = [];

            if ($field->datatype === 'menu') {
                // Extraire les lignes non vides de param1.
                $raw  = $field->param1 ?? '';
                $opts = array_values(array_filter(array_map('trim', explode("\n", $raw)), function($v) {
                    return $v !== '' && $v !== null;
                }));
                if (empty($opts)) {
                    // Aucune valeur utilisable : ne pas exposer ce champ.
                    continue;
                }
                $values = $opts;
            } elseif ($field->datatype === 'checkbox') {
                $values = ['0', '1'];
            } elseif ($field->datatype === 'text') {
                // Champ texte libre : interroger la BD pour toutes les valeurs
                // distinctes non nulles effectivement saisies par les utilisateurs.
                $sql = "SELECT DISTINCT d.data
                          FROM {user_info_data} d
                          JOIN {user_info_field} f ON f.id = d.fieldid
                         WHERE f.shortname = :shortname
                           AND d.data IS NOT NULL
                           AND d.data <> ''
                         ORDER BY d.data";
                $dbvalues = $DB->get_fieldset_sql($sql, ['shortname' => $field->shortname]);
                $values   = array_values($dbvalues);

                // Rétro-compatibilité : inclure les valeurs précédemment configurées
                // manuellement mais absentes de la BD (cas de migration).
                if ($field->shortname === $selectedfield && !empty($selectedvalues)) {
                    $values = array_values(array_unique(array_merge($values, $selectedvalues)));
                    sort($values);
                }
            } else {
                // Types non supportés (textarea, datetime, etc.) : ignorer.
                continue;
            }

            $fieldsdata[] = [
                'shortname' => $field->shortname,
                'name'      => format_string($field->name),
                'datatype'  => $field->datatype,
                'values'    => $values,
                // The user can type this value in their own profile, so the targeting is no
                // restriction (SAST L-069011, accepted risk : locking the field is the site's
                // choice). Unlocked and not "Not visible" (0) : the edit form offers it,
                // profile_field_base::is_editable() and edit_field_set_locked().
                'usereditable' => (int)$field->locked === 0 && (int)$field->visible !== 0,
            ];
            $fieldoptions[$field->shortname] = format_string($field->name) . ' (' . $field->datatype . ')';
        }

        // ── Build HTML ────────────────────────────────────────────────────
        $inputname = $this->get_full_name();
        $inputid   = $this->get_id();
        $idx       = $this->newsindex;

        $html  = \html_writer::start_div('newsfilter-widget', [
            'id'               => 'newsfilter-widget-' . $idx,
            'data-newsindex'   => $idx,
            'data-fieldsdata'  => json_encode($fieldsdata, JSON_HEX_APOS | JSON_HEX_QUOT),
        ]);

        // Hidden input that carries the JSON value on form submit.
        $html .= \html_writer::empty_tag('input', [
            'type'  => 'hidden',
            'name'  => $inputname,
            'id'    => $inputid,
            'value' => $rawdata ?: '{}',
        ]);

        // ── Row 1 : Enable checkbox ───────────────────────────────────────
        $html .= \html_writer::start_div('newsfilter-row newsfilter-enable-row');
        $cbattrs = [
            'type'  => 'checkbox',
            'id'    => $inputid . '_enabled',
            'class' => 'newsfilter-enable-cb',
            'value' => '1',
        ];
        if ($enabled) {
            $cbattrs['checked'] = 'checked';
        }
        $html .= \html_writer::empty_tag('input', $cbattrs);
        $html .= ' ';
        $html .= \html_writer::tag('label',
            get_string('newsfilter_enable', 'block_newsslider'),
            ['for' => $inputid . '_enabled']
        );
        $html .= \html_writer::end_div();

        // ── Row 2 : Profile field selector ───────────────────────────────
        $fieldrowstyle = $enabled ? '' : 'display:none;';
        $html .= \html_writer::start_div('newsfilter-row newsfilter-field-row', ['style' => $fieldrowstyle]);

        if (empty($profilefields)) {
            $html .= \html_writer::tag('em', get_string('newsfilter_noprofilefields', 'block_newsslider'));
        } else {
            $html .= \html_writer::tag('label',
                get_string('newsfilter_field', 'block_newsslider') . ' : ',
                ['for' => $inputid . '_field', 'class' => 'newsfilter-label']
            );
            $html .= \html_writer::select(
                $fieldoptions,
                $inputname . '_field',
                $selectedfield,
                false,
                ['id' => $inputid . '_field', 'class' => 'newsfilter-field-select custom-select']
            );
        }
        $html .= \html_writer::end_div();

        // ── Row 3 : Value selector (checkboxes per field) ─────────────────
        $valuesrowstyle = ($enabled && !empty($selectedfield)) ? '' : 'display:none;';
        $html .= \html_writer::start_div('newsfilter-row newsfilter-values-row', ['style' => $valuesrowstyle]);
        $html .= \html_writer::tag('label',
            get_string('newsfilter_values', 'block_newsslider') . ' : ',
            ['class' => 'newsfilter-label']
        );
        $html .= \html_writer::start_div('newsfilter-values-list');

        foreach ($fieldsdata as $fieldinfo) {
            $groupstyle = ($fieldinfo['shortname'] === $selectedfield) ? '' : 'display:none;';
            $html .= \html_writer::start_div('newsfilter-values-group', [
                'data-field' => $fieldinfo['shortname'],
                'style'      => $groupstyle,
            ]);

            // Inside the group, so that the existing JS shows it with the field's values only.
            if ($fieldinfo['usereditable']) {
                $html .= \html_writer::div(
                    get_string('newsfilter_usereditable', 'block_newsslider'),
                    'alert alert-warning newsfilter-usereditable'
                );
            }

            if (empty($fieldinfo['values'])) {
                // Aucune valeur disponible : aucun utilisateur n'a encore renseigné ce champ.
                $html .= \html_writer::tag('em',
                    get_string('newsfilter_novalues', 'block_newsslider'),
                    ['class' => 'text-muted']
                );
            } else {
                // Afficher les valeurs sous forme de cases à cocher.
                foreach ($fieldinfo['values'] as $val) {
                    $checked = in_array((string)$val, $selectedvalues, true);
                    $cbid    = $inputid . '_val_' . $fieldinfo['shortname'] . '_' . md5($val);
                    $cbattrs2 = [
                        'type'       => 'checkbox',
                        'id'         => $cbid,
                        'class'      => 'newsfilter-val-cb',
                        'data-field' => $fieldinfo['shortname'],
                        'value'      => $val,
                    ];
                    if ($checked) {
                        $cbattrs2['checked'] = 'checked';
                    }
                    $html .= \html_writer::start_div('newsfilter-value-item');
                    $html .= \html_writer::empty_tag('input', $cbattrs2);
                    $html .= ' ';
                    $html .= \html_writer::tag('label', s($val), ['for' => $cbid]);
                    $html .= \html_writer::end_div();
                }
            }

            $html .= \html_writer::end_div(); // .newsfilter-values-group
        }

        $html .= \html_writer::end_div(); // .newsfilter-values-list
        $html .= \html_writer::end_div(); // .newsfilter-values-row

        $html .= \html_writer::end_div(); // .newsfilter-widget

        return format_admin_setting($this, $this->visiblename, $html, $this->description, $inputid, '', '', $query);
    }
}

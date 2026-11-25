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
 * Page de paramétrage du plugin accessible aux managers sans accès admin général.
 * Les admins sont redirigés vers la page admin standard.
 *
 * URL : /blocks/newsslider/managesettings.php
 *
 * @package     block_newsslider
 * @author      BORNET Stéphen.
 * @copyright   2023 YMAG info@ymag.fr https://www.ymag.fr/
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/blocks/newsslider/lib.php');

require_login();
require_capability('block/newsslider:managesettings', context_system::instance());

// Les admins ont accès à la page standard et y sont redirigés.
if (is_siteadmin()) {
    redirect(new moodle_url('/admin/settings.php', ['section' => 'blocksettingnewsslider']));
}

$pageurl = new moodle_url('/blocks/newsslider/managesettings.php');

$PAGE->set_context(context_system::instance());
$PAGE->set_url($pageurl);
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('pluginname', 'block_newsslider'));
$PAGE->set_heading(get_string('pluginname', 'block_newsslider'));

// Construction de la page de paramétrage (même définition que settings.php).
$settingspage = new admin_settingpage(
    'blocksettingnewsslider',
    get_string('pluginname', 'block_newsslider'),
    'block/newsslider:managesettings'
);
block_newsslider_populate_settings($settingspage);

// Traitement de la soumission du formulaire.
$notice = '';
$saveerrors = [];
if ($data = data_submitted()) {
    require_sesskey();
    foreach ($settingspage->settings as $setting) {
        $fullname = $setting->get_full_name();
        // Les cases à cocher absentes du POST (décochées) reçoivent une chaîne vide ;
        // chaque admin_setting::write_setting() gère ce cas (ex. checkbox → valeur 'no').
        $value = isset($data->$fullname) ? $data->$fullname : '';
        $error = $setting->write_setting($value);
        if ($error !== '') {
            $saveerrors[$fullname] = $error;
        }
    }
    if (empty($saveerrors)) {
        $notice = get_string('changessaved');
    } else {
        $notice = implode('<br>', $saveerrors);
    }
}

// Affichage.
echo $OUTPUT->header();

if ($notice !== '') {
    echo $OUTPUT->notification($notice, empty($saveerrors) ? 'success' : 'error');
}

// Rendu du formulaire : chaque admin_setting génère son propre HTML via output_html().
// Le formulaire pointe sur cette même page (pas sur /admin/settings.php).
echo html_writer::start_tag('form', [
    'method'  => 'post',
    'action'  => $pageurl->out(false),
    'id'      => 'adminsettings',
    'enctype' => 'multipart/form-data',
]);
echo html_writer::empty_tag('input', [
    'type'  => 'hidden',
    'name'  => 'sesskey',
    'value' => sesskey(),
]);

echo html_writer::start_tag('div', ['class' => 'adminsettings']);

foreach ($settingspage->settings as $setting) {
    echo $setting->output_html($setting->get_setting());
}

echo html_writer::end_tag('div');

echo html_writer::tag(
    'div',
    html_writer::empty_tag('input', [
        'type'  => 'submit',
        'value' => get_string('savechanges'),
        'class' => 'btn btn-primary',
        'id'    => 'adminsettings_save',
    ]),
    ['class' => 'form-group row fitem mt-3']
);

echo html_writer::end_tag('form');

echo $OUTPUT->footer();

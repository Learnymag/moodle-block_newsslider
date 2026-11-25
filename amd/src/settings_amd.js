// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @module     block_newsslider/settings_amd
 * @author     BORNET Stéphen.
 * @copyright  2024 YMAG info@ymag.fr https://www.ymag.fr/
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @thanks     Volodymyr DOVHAN for his help & his patience.
 */

define([], function () {

    // ────────────────────────────────────────────────────────────────────────
    // Gestion des widgets de ciblage utilisateur
    // ────────────────────────────────────────────────────────────────────────

    /**
     * Initialise tous les widgets .newsfilter-widget présents sur la page.
     * Chaque widget lit ses données de champs depuis data-fieldsdata (JSON).
     * Les changements de l'utilisateur sérialisent le widget en JSON et
     * mettent à jour l'input caché correspondant.
     */
    function initTargetFilters() {
        document.querySelectorAll('.newsfilter-widget').forEach(function(widget) {
            var hiddenInput  = widget.querySelector('input[type="hidden"]');
            var enableCb     = widget.querySelector('.newsfilter-enable-cb');
            var fieldSelect  = widget.querySelector('.newsfilter-field-select');
            var fieldRow     = widget.querySelector('.newsfilter-field-row');
            var valuesRow    = widget.querySelector('.newsfilter-values-row');

            if (!hiddenInput) {
                return;
            }

            /**
             * Sérialise l'état courant du widget vers l'input caché.
             */
            function serialize() {
                var enabled = enableCb ? enableCb.checked : false;
                var field   = fieldSelect ? fieldSelect.value : '';
                var values  = [];

                if (field) {
                    widget.querySelectorAll(
                        '.newsfilter-val-cb[data-field="' + CSS.escape(field) + '"]:checked'
                    ).forEach(function(cb) {
                        values.push(cb.value);
                    });
                }

                hiddenInput.value = JSON.stringify({ enabled: enabled, field: field, values: values });
            }

            // Checkbox "activer"
            if (enableCb) {
                enableCb.addEventListener('change', function() {
                    if (fieldRow) {
                        fieldRow.style.display = this.checked ? '' : 'none';
                    }
                    if (!this.checked && valuesRow) {
                        valuesRow.style.display = 'none';
                    }
                    if (this.checked && fieldSelect && fieldSelect.value && valuesRow) {
                        // Afficher les valeurs uniquement si un champ est déjà sélectionné
                        var hasGroup = widget.querySelector(
                            '.newsfilter-values-group[data-field="' + CSS.escape(fieldSelect.value) + '"]'
                        );
                        valuesRow.style.display = hasGroup ? '' : 'none';
                    }
                    serialize();
                });
            }

            // Select du champ de profil
            if (fieldSelect) {
                fieldSelect.addEventListener('change', function() {
                    var selectedField = this.value;

                    // Masquer tous les groupes de valeurs
                    widget.querySelectorAll('.newsfilter-values-group').forEach(function(g) {
                        g.style.display = 'none';
                    });

                    if (selectedField && valuesRow) {
                        var group = widget.querySelector(
                            '.newsfilter-values-group[data-field="' + CSS.escape(selectedField) + '"]'
                        );
                        if (group) {
                            valuesRow.style.display = '';
                            group.style.display     = '';
                        }
                    } else if (valuesRow) {
                        valuesRow.style.display = 'none';
                    }

                    serialize();
                });
            }

            // Checkboxes des valeurs.
            widget.querySelectorAll('.newsfilter-val-cb').forEach(function(cb) {
                cb.addEventListener('change', serialize);
            });
        });
    }

    /**
     * Retourne les sélecteurs de TOUS les réglages d'une actualité.
     *
     * LA seule liste : le repli initial et la bascule au clic l'appellent tous les deux.
     * Un réglage ajouté dans lib.php et oublié ici reste visible section repliée — panne
     * silencieuse, et c'est exactement ce que deux listes séparées ont déjà produit.
     *
     * Le core forme l'id de ligne « admin- » + nom du réglage (adminlib.php,
     * format_admin_setting()), soit « admin-newstitle02 ». L'index arrive DÉJÀ sur deux
     * chiffres : n'ajouter aucun « 0 » devant. Jusqu'à la 0.5.2, chaque sélecteur en
     * portait un de trop (« #admin-newstitle002 »), aucun ne matchait et l'accordéon ne
     * repliait rien — sans la moindre erreur.
     *
     * @param {string} padded L'index de l'actualité sur deux chiffres ('02' … '06').
     * @return {string[]} Les sélecteurs CSS des lignes de réglage à masquer ou révéler.
     */
    function sectionSelectors(padded) {
        return [
            '#admin-newstitle'     + padded,
            '#admin-newstagcolour' + padded,
            '#admin-newsdate'      + padded,
            '#admin-newsheadline'  + padded,
            '#admin-newscontent'   + padded,
            '#admin-newsurl'       + padded,
            '#admin-newstarget'    + padded,
            '#admin-newsimages'    + padded,
            '#admin-newsvideourl'  + padded,
            '#admin-newsfilter'    + padded,
        ];
    }

    return {
        /**
         * Point d'entrée appelé par Moodle via js_call_amd.
         * Le DOM est déjà prêt à ce stade — pas besoin de DOMContentLoaded.
         */
        init: function () {

            // ── Accordion : masquer les blocs 2-6 par défaut ──────────────
            for (var idx = 2; idx <= 6; idx++) {
                var padded = idx < 10 ? '0' + idx : '' + idx;
                var selectors = sectionSelectors(padded);
                selectors.forEach(function(sel) {
                    var el = document.querySelector(sel);
                    if (el) {
                        el.style.display = 'none';
                    }
                });
            }

            // ── Accordion : toggle au clic sur le heading ────────────────
            document.querySelectorAll('.headingsettings').forEach(function(heading) {
                heading.addEventListener('click', function() {
                    var isWrapped = this.matches('[data-sttngstts="wrapped"]');
                    var idx       = this.getAttribute('data-sttngid');
                    var padded    = parseInt(idx) < 10 ? '0' + idx : '' + idx;

                    var sels = sectionSelectors(padded);

                    if (isWrapped) {
                        this.setAttribute('data-sttngstts', 'unwrapped');
                        this.querySelector('i').classList.remove('bi-arrow-right-square-fill');
                        this.querySelector('i').classList.add('bi-arrow-down-square-fill');
                        sels.forEach(function(sel) {
                            var el = document.querySelector(sel);
                            if (el) { el.style.display = 'flex'; }
                        });
                    } else {
                        this.setAttribute('data-sttngstts', 'wrapped');
                        this.querySelector('i').classList.remove('bi-arrow-down-square-fill');
                        this.querySelector('i').classList.add('bi-arrow-right-square-fill');
                        sels.forEach(function(sel) {
                            var el = document.querySelector(sel);
                            if (el) { el.style.display = 'none'; }
                        });
                    }
                }, false);
            });

            // ── Targeting filter widgets ──────────────────────────────────
            initTargetFilters();
        }
    };
});

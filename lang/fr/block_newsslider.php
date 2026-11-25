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
$string['greetings'] = 'Bienvenue dans l\'éditeur de News Slider!';
$string['mandatoryfields'] = '<span class="mandatoryfields">*</span>';
$string['mandatoryfieldsdesc'] = '<span class="mandatoryfields">* : Champs obligatoires.</span>';
$string['newscolour'] = 'Couleur Personnalisable';
$string['newscolourdesc'] = 'Couleur qui concerne la bloc de titre, la catégorie ainsi que les points de navigation servant de feedback.';
$string['newscontent'] = 'Contenu de l\'actualité';
$string['newscontentdesc'] = 'Descriptif, en une seule phrase, sans saut de ligne, de l\'actualité n°';
$string['newscontentdefault'] = 'Exemple de Contenu';
$string['newscycle'] = 'Temps de cycle';
$string['newscycledesc'] = 'Temps d\'affichage en millisecondes d\'un élément avant permutation (6000 ms = 6 secondes).';
$string['newscycledefault'] = '6000';
$string['newsheading'] = 'Actualités';
$string['newslabel'] = 'Intitulé du bloc';
$string['newslabeldesc'] = 'Label situé à gauche du bloc.';
$string['newslabeldefault'] = 'Actualités';
$string['newstagcolour'] = 'Couleur de l\'étiquette';
$string['newstagcolourdesc'] = 'Couleur de l\'étiquette qui affiche le titre de l\'actualité. Laissée sur la couleur du bloc, l\'étiquette prend la couleur personnalisable ci-dessus.';
$string['newstagcolourdefault'] = 'Couleur par défaut du bloc';
$string['tagcolour_blue'] = 'Bleu';
$string['tagcolour_cyan'] = 'Cyan';
$string['tagcolour_green'] = 'Vert';
$string['tagcolour_yellow'] = 'Jaune';
$string['tagcolour_pink'] = 'Rose';
$string['tagcolour_red'] = 'Rouge';
$string['tagcolour_orange'] = 'Orange';
$string['tagcolour_teal'] = 'Bleu canard';
$string['tagcolour_purple'] = 'Violet';
$string['tagcolour_indigo'] = 'Indigo';
$string['tagcolour_neutral'] = 'Neutre';
$string['newstarget'] = 'Comportement du lien';
$string['newstargetdesc'] = 'Ouvrir dans une nouvelle fenêtre le lien n°';
$string['newstitle'] = 'Titre de l\'actualité';
$string['newstitledesc'] = 'Concerne la catégorie lié à l\'actualité n°';
$string['newstitledefault'] = 'Exemple de titre';
$string['newsurl'] = 'Lien de l\'actualité';
$string['newsurldesc'] = 'URL de l\'actualité n°';
$string['newsurldefault'] = 'https://www.ymag.fr';

// ── List display mode strings ─────────────────────────────────────────────────
$string['listmode'] = 'Affichage en liste';
$string['listmodedesc'] = 'Affiche les actualités sous forme de liste datée plutôt qu\'en carrousel. Chaque entrée présente sa catégorie, sa date, son titre et son extrait, les unes sous les autres. Les images et les vidéos ne sont pas affichées dans ce mode, mais elles sont conservées et réapparaissent au retour du carrousel.';
$string['allnewspage'] = 'Page de toutes les actualités';
$string['allnewspagedesc'] = 'Ajoute un lien « Toutes les actualités » en pied du bloc, dans les deux modes d\'affichage. Il ouvre une page qui liste chaque actualité avec son média (image, diaporama ou vidéo) et son contenu complet. Une actualité ciblée n\'y reste visible que des utilisateurs auxquels elle s\'adresse.';
$string['allnewspagedisabled'] = 'La page de toutes les actualités n\'est pas activée sur ce site.';
$string['readmore'] = 'En savoir +';
$string['nonews'] = 'Aucune actualité à afficher.';
$string['prevslide'] = 'Image précédente';
$string['nextslide'] = 'Image suivante';
$string['gotoslide'] = 'Image {$a}';
$string['allnews'] = 'Toutes les actualités';
$string['newsdate'] = 'Date de l\'actualité';
$string['newsdatedesc'] = 'Date affichée en mode d\'affichage en liste, pour l\'actualité n°';
$string['newsheadline'] = 'Titre de l\'actualité (mode liste)';
$string['newsheadlinedesc'] = 'Titre affiché en gras en mode d\'affichage en liste, au-dessus du contenu qui sert alors d\'extrait. Laissé vide, c\'est le contenu qui sert de titre et aucun extrait n\'est affiché. Actualité n°';

// ── Media strings ─────────────────────────────────────────────────────────────
$string['newsimages'] = 'Images de l\'actualité';
$string['newsimagesdesc'] = 'Importez jusqu\'à 5 images pour cette actualité. Si plusieurs images sont ajoutées, un carrousel avec flèches de navigation sera affiché. Formats acceptés : JPG, PNG, GIF, WebP.';
$string['newsvideourl'] = 'URL Vidéo de l\'actualité';
$string['newsvideourldesc'] = 'URL d\'une vidéo YouTube, Vimeo, ou d\'un fichier vidéo direct (mp4, webm, ogg). Laissez vide si aucune vidéo. Note : si une image ET une vidéo sont fournies, l\'image est prioritaire.';

// ── Targeting / user-profile filter strings ───────────────────────────────────
$string['newsfilter'] = 'Filtrage utilisateur de l\'actualité';
$string['newsfilterdesc'] = 'Permet de restreindre l\'affichage de cette actualité aux utilisateurs dont un champ de profil personnalisé correspond à une valeur spécifique.';
$string['newsfilter_enable'] = 'Activer le filtrage par champ de profil utilisateur';
$string['newsfilter_field'] = 'Champ de profil utilisateur';
$string['newsfilter_selectfield'] = '-- Sélectionner un champ --';
$string['newsfilter_values'] = 'Valeurs recherchées';
$string['newsfilter_noprofilefields'] = 'Aucun champ de profil personnalisé n\'est configuré dans Moodle. Rendez-vous dans Administration du site > Utilisateurs > Champs de profil.';
$string['newsfilter_novalues'] = 'Aucune valeur trouvée dans les profils utilisateurs pour ce champ. L\'actualité sera visible par tous jusqu\'à ce qu\'au moins un utilisateur renseigne ce champ.';
$string['newsfilter_usereditable'] = 'Ce champ est modifiable par l\'utilisateur depuis son profil : il peut se donner lui-même une valeur ciblée. Le filtre trie alors les actualités, il ne les restreint pas. Pour en faire une restriction, verrouillez le champ dans Administration du site > Utilisateurs > Champs de profil. Le verrou ne fige que le formulaire de modification du profil : les synchronisations et les services web continuent d\'écrire dans le champ.';

// ── Privacy & capabilities ────────────────────────────────────────────────────
$string['privacy:metadata'] = 'Le plugin News Slider plugin ne conserve aucune donnée personnelle d\'aucun utilisateur potentiel.';
$string['myaddinstance'] = 'Permettre d\'ajouter un bloc News Slider dans la page Tableau de bord';
$string['addinstance'] = 'Permettre d\'ajouter un bloc News Slider';
$string['newsslider:myaddinstance'] = 'Permettre d\'ajouter un bloc News Slider dans la page Tableau de bord';
$string['newsslider:addinstance'] = 'Permettre d\'ajouter un bloc News Slider';
$string['newsslider:managesettings'] = 'Gérer les paramètres du plugin News Slider';

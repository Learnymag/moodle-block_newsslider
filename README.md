# News Slider (EN) #

## Overview ##
"News Slider" plugin for Moodle allows you to add a customizable scrollable news banner on pages, which can for example display your course or site announcements.

## Features ##

1. **Simplicity & Compatibility**: A text block easy to set up and compatible with different Moodle themes.

2. **Customizable color**: The accent color is configurable to adapt to your graphic charter (affects the label, category labels and navigation dots).

3. **Up to 6 simultaneous announcements**: Each news item can contain a title/category, a text description, and an optional link (opens in the same tab or a new tab).

4. **Images with carousel**: Each news item supports up to 5 images. When multiple images are uploaded, navigation arrows and dot indicators appear inside the item, allowing users to browse through them.

5. **Video support**: Each news item can optionally include a video. Supported sources:
   - YouTube (URL is automatically converted to a privacy-friendly embed)
   - Vimeo (URL is automatically converted to an embed)
   - Direct video file (mp4, webm, ogg) rendered with a native `<video>` element

6. **Uniform height**: If at least one news item has a media element (image or video), all items — including text-only ones — adopt the same fixed height, preventing the block from resizing during transitions.

7. **User-profile targeting**: Each news item can be restricted to users who match specific values in a custom Moodle user profile field. When enabled, the item is only shown if the logged-in user's profile field value is among the allowed values. Site administrators always see all items regardless of the filter.

8. **"FilterCodes friendly"**: Combine this plugin with the FilterCodes text filter to inject user-specific information into news content.

---

## Settings reference ##

### Global settings ###
| Setting | Description |
|---|---|
| Customizable color | Accent color used for the label block, category text, and navigation dots |
| Block title | Text displayed in the left (or top) label |
| Cycle time | Delay in milliseconds before the slider moves to the next item |

### Per-news settings (News 1–6) ###
News items 2–6 are collapsed by default and can be expanded by clicking the heading.

| Setting | Description |
|---|---|
| News title | Category label displayed above the content |
| Tag colour | Optional named colour of this news item's tag (blue, cyan, green, yellow, pink, red, orange, teal, purple, indigo, neutral). Left on "Block colour", the tag keeps the customizable color |
| News content | Main text of the news item |
| News link | URL the item links to (leave as `#` for no link) |
| Open in new tab | Whether the link opens in a new browser tab |
| News images | File manager – upload up to 5 images; a carousel is shown if >1 image |
| Video URL | YouTube, Vimeo, or direct video file URL |
| User targeting filter | See below |

### User-profile targeting filter ###
1. **Enable**: Check "Enable filtering by user profile field".
2. **Field**: Select one of the available custom profile fields (types `menu`, `checkbox`, or `text` are shown).
3. **Allowed values**: Check every value that should be allowed. Only users whose profile field contains one of the checked values will see this news item.

> The filter is evaluated server-side in `get_content()`. Site administrators bypass it and always see all items.

---

## Installation via uploaded ZIP file ##

1. Log in to your Moodle site as an admin and go to _Site administration > Plugins > Install plugins_.
2. Upload the ZIP file. You should only be prompted to add extra details if the plugin type is not automatically detected.
3. Check the plugin validation report and finish the installation.

## Installing manually ##

Put the contents of this directory into:

    {your/moodle/dirroot}/blocks/newsslider

Then log in as admin and go to _Site administration > Notifications_ to complete the installation.

Or run:

    $ php admin/cli/upgrade.php

## Developer notes ##

- **AMD build files**: The files in `amd/build/` must be regenerated after any change to `amd/src/` by running `grunt amd` from the Moodle root. The build files shipped in this repository are equivalent to the source files and will work in both development and production mode.
- **SCSS**: `styles.css` is compiled from `styles.scss`. After modifying the SCSS, regenerate the CSS (e.g. with `sass styles.scss styles.css`).
- **Image file areas**: Images are stored in the `block_newsslider` component, file areas named `newsimage01` … `newsimage06`, served by the `block_newsslider_pluginfile()` function in `lib.php`.
- **Targeting data format**: The targeting filter stores its configuration as a JSON string: `{"enabled":true,"field":"department","values":["IT","HR"]}`.

## License ##

2024 YMAG info@ymag.fr https://www.ymag.fr/

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program. If not, see <https://www.gnu.org/licenses/>.

---------------------------------------------------------------------------------------------------------------------------------------------------------

# News Slider (FR) #

## Présentation ##
Le plugin "News Slider" pour Moodle permet d'ajouter un bandeau d'actualités défilantes personnalisable sur les pages, pouvant afficher des annonces de cours ou du site.

## Fonctionnalités ##

1. **Simplicité & Compatibilité** : Un bloc simple à mettre en place, compatible avec les différents thèmes Moodle.

2. **Couleur personnalisable** : La couleur d'accentuation est paramétrable pour s'adapter à votre charte graphique (label, catégories, points de navigation).

3. **Jusqu'à 6 actualités simultanées** : Chaque actualité peut contenir un titre/catégorie, un texte descriptif, et un lien optionnel (même onglet ou nouvel onglet).

4. **Images avec carrousel** : Chaque actualité accepte jusqu'à 5 images. Si plusieurs images sont présentes, des flèches de navigation et des indicateurs apparaissent à l'intérieur du bloc pour naviguer entre elles.

5. **Support vidéo** : Chaque actualité peut inclure une vidéo. Sources prises en charge :
   - YouTube (l'URL est automatiquement convertie en embed respectueux de la vie privée)
   - Vimeo (l'URL est automatiquement convertie en embed)
   - Fichier vidéo direct (mp4, webm, ogg) rendu avec l'élément natif `<video>`

6. **Hauteur uniforme** : Si au moins une actualité possède un média (image ou vidéo), toutes les actualités — y compris les actualités texte uniquement — adoptent la même hauteur fixe, évitant que le bloc ne change de taille lors des transitions.

7. **Ciblage par champ de profil utilisateur** : Chaque actualité peut être restreinte aux utilisateurs qui correspondent à des valeurs spécifiques d'un champ de profil personnalisé Moodle. L'actualité n'est affichée que si la valeur du champ de profil de l'utilisateur connecté figure parmi les valeurs autorisées. Les administrateurs du site voient toujours toutes les actualités, quel que soit le filtre.

8. **"FilterCodes friendly"** : Combinez ce plugin avec le filtre textuel FilterCodes pour injecter des informations spécifiques à l'utilisateur dans le contenu des actualités.

---

## Référence des paramètres ##

### Paramètres globaux ###
| Paramètre | Description |
|---|---|
| Couleur personnalisable | Couleur d'accentuation utilisée pour le label, les catégories et les points de navigation |
| Intitulé du bloc | Texte affiché dans le label à gauche (ou en haut en mode drawer) |
| Temps de cycle | Délai en millisecondes avant le passage à l'actualité suivante |

### Paramètres par actualité (Actualités 1–6) ###
Les actualités 2 à 6 sont réduites par défaut et peuvent être dépliées en cliquant sur leur en-tête.

| Paramètre | Description |
|---|---|
| Titre de l'actualité | Libellé de catégorie affiché au-dessus du contenu |
| Couleur de l'étiquette | Couleur nommée facultative de l'étiquette de cette actualité (bleu, cyan, vert, jaune, rose, rouge, orange, bleu canard, violet, indigo, neutre). Laissée sur « Couleur par défaut du bloc », l'étiquette garde la couleur personnalisable |
| Contenu de l'actualité | Texte principal de l'actualité |
| Lien de l'actualité | URL vers laquelle pointe l'actualité (`#` pour aucun lien) |
| Ouvrir dans un nouvel onglet | Si le lien doit s'ouvrir dans un nouvel onglet |
| Images de l'actualité | Gestionnaire de fichiers – jusqu'à 5 images ; un carrousel s'affiche si > 1 image |
| URL Vidéo | URL YouTube, Vimeo, ou fichier vidéo direct |
| Filtrage utilisateur | Voir ci-dessous |

### Filtrage par champ de profil utilisateur ###
1. **Activer** : Cocher "Activer le filtrage par champ de profil utilisateur".
2. **Champ** : Sélectionner l'un des champs de profil personnalisés disponibles (les types `menu`, `checkbox` et `text` sont affichés).
3. **Valeurs autorisées** : Cocher chaque valeur autorisée. Seuls les utilisateurs dont le champ de profil contient l'une des valeurs cochées verront cette actualité.

> Le filtre est évalué côté serveur dans `get_content()`. Les administrateurs du site le contournent et voient toujours toutes les actualités.

---

## Installation via un fichier ZIP téléchargé ##

1. Connectez-vous à votre site Moodle en tant qu'administrateur et allez à _Administration du site > Plugins > Installer des plugins_.
2. Téléchargez le fichier ZIP. Vérifiez le rapport de validation et terminez l'installation.

## Installation manuelle ##

Placez le contenu de ce répertoire dans :

    {votre/répertoire/racine/moodle}/blocks/newsslider

Connectez-vous ensuite en tant qu'admin et allez à _Administration du site > Notifications_.

Ou exécutez :

    $ php admin/cli/upgrade.php

## Notes développeur ##

- **Fichiers AMD build** : Les fichiers dans `amd/build/` doivent être régénérés après toute modification des sources `amd/src/` en exécutant `grunt amd` depuis la racine Moodle. Les fichiers livrés dans ce dépôt sont équivalents aux sources et fonctionneront en mode développement et production.
- **SCSS** : `styles.css` est compilé depuis `styles.scss`. Après modification du SCSS, régénérer le CSS (ex. : `sass styles.scss styles.css`).
- **File areas images** : Les images sont stockées dans le composant `block_newsslider`, dans les file areas `newsimage01` … `newsimage06`, servies par `block_newsslider_pluginfile()` dans `lib.php`.
- **Format des données de ciblage** : Le filtre de ciblage stocke sa configuration en JSON : `{"enabled":true,"field":"service","values":["RH","IT"]}`.

## Licence ##

2024 YMAG info@ymag.fr https://www.ymag.fr/

Ce programme est un logiciel libre : vous pouvez le redistribuer et/ou le modifier
selon les termes de la Licence publique générale GNU publiée par la Free Software
Foundation, soit la version 3 de la Licence, soit (selon votre choix) une version
ultérieure.

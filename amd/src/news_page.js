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
 * Diaporamas de la page « Toutes les actualités » (news.php).
 *
 * Volontairement indépendant de news_amd : ce dernier suppose le balisage du carrousel
 * (#dotContainer, #actualitesList) et lèverait sur cette page. Ici, tout est accroché
 * aux data-region / data-action du gabarit news_page, sans id.
 *
 * @module     block_newsslider/news_page
 * @author     BORNET Stéphen.
 * @copyright  2026 YMAG info@ymag.fr https://www.ymag.fr/
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([], function() {

    /**
     * Branche un diaporama : flèches, points, et flèches du clavier quand il a le focus.
     * Pas de défilement automatique — même comportement que le carrousel d'images du bloc.
     *
     * @param {HTMLElement} slideshow L'élément [data-region="nsl-slideshow"].
     */
    function initSlideshow(slideshow) {
        var track = slideshow.querySelector('[data-region="nsl-track"]');
        var slides = slideshow.querySelectorAll('.nsl-page__slide');
        var dots = slideshow.querySelectorAll('[data-action="nsl-goto"]');

        if (!track || slides.length <= 1) {
            return;
        }

        var current = 0;

        /**
         * Affiche l'image n (bouclage aux deux extrémités).
         *
         * @param {Number} n Index visé.
         */
        function goTo(n) {
            current = (n + slides.length) % slides.length;
            track.style.transform = 'translateX(-' + (current * 100) + '%)';
            dots.forEach(function(dot, i) {
                var active = i === current;
                dot.classList.toggle('is-active', active);
                dot.setAttribute('aria-current', active ? 'true' : 'false');
            });
        }

        var prev = slideshow.querySelector('[data-action="nsl-prev"]');
        var next = slideshow.querySelector('[data-action="nsl-next"]');

        if (prev) {
            prev.addEventListener('click', function() {
                goTo(current - 1);
            });
        }
        if (next) {
            next.addEventListener('click', function() {
                goTo(current + 1);
            });
        }

        dots.forEach(function(dot, i) {
            dot.addEventListener('click', function() {
                goTo(i);
            });
        });

        slideshow.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft') {
                goTo(current - 1);
            } else if (e.key === 'ArrowRight') {
                goTo(current + 1);
            }
        });

        goTo(0);
    }

    return {
        /**
         * Point d'entrée appelé par news.php via js_call_amd.
         */
        init: function() {
            document.querySelectorAll('[data-region="nsl-slideshow"]').forEach(initSlideshow);
        }
    };
});

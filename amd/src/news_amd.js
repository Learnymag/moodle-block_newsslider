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
 * @module     block_newsslider/news_amd
 * @author     BORNET Stéphen.
 * @copyright  2024 YMAG info@ymag.fr https://www.ymag.fr/
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @thanks     Volodymyr DOVHAN for his help & his patience.
 */

define([], function () {

    const globalNewsSettngsDatas = [];
    // Booléen : l'utilisateur a-t-il cliqué sur un point de navigation ?
    var dotClicked = false;
    // Booléen : le défilement est-il en pause (survol de la souris) ?
    var paused = false;

    // ────────────────────────────────────────────────────────────────────────
    // Mode responsive : bascule la classe .is-narrow selon la largeur du bloc
    // ────────────────────────────────────────────────────────────────────────

    /**
     * Observe la largeur du bloc et ajoute/retire .is-narrow sur l'élément
     * section[data-block=newsslider].
     * < 500px → mode carré (colonne, centré, animation fade)
     * ≥ 500px → mode rectangulaire (côte à côte, animation glide)
     */
    function initResponsive() {
        var block = document.querySelector('section[data-block="newsslider"]');
        if (!block) {
            return;
        }

        function updateNarrow() {
            // Filet de sécurité : dans un drawer ou une région "outside",
            // le bloc reste toujours en mode narrow, quelle que soit sa largeur.
            var forcedNarrow = !!block.closest(
                '.drawer, aside#block-region-outside-right, aside#block-region-outside-left'
            );
            if (forcedNarrow || block.getBoundingClientRect().width < 500) {
                block.classList.add('is-narrow');
            } else {
                block.classList.remove('is-narrow');
            }
            applyUniformHeight();
        }

        // Vérification initiale synchrone (avant le premier rendu de boucle)
        updateNarrow();

        // Réagit aux redimensionnements ultérieurs
        if (window.ResizeObserver) {
            var ro = new ResizeObserver(function() {
                updateNarrow();
            });
            ro.observe(block);
        }
    }

    // ────────────────────────────────────────────────────────────────────────
    // Carousel interne à chaque actualité (images multiples)
    // ────────────────────────────────────────────────────────────────────────

    /**
     * Initialise les carrousels d'images dans chaque .news-carousel.
     */
    function initCarousels() {
        document.querySelectorAll('.news-carousel').forEach(function(carousel) {
            const track  = carousel.querySelector('.carousel-track');
            const imgs   = carousel.querySelectorAll('.carousel-img');
            const dots   = carousel.querySelectorAll('.carousel-dot');

            if (!track || imgs.length <= 1) {
                return; // Pas de carousel pour une seule image
            }

            let current = 0;

            function goTo(n) {
                current = (n + imgs.length) % imgs.length;
                track.style.transform = 'translateX(-' + (current * 100) + '%)';
                // Mise à jour des indicateurs
                dots.forEach(function(dot, i) {
                    dot.classList.toggle('active', i === current);
                });
            }

            // Initialisation des indicateurs
            if (dots.length > 0) {
                dots[0].classList.add('active');
            }

            const prevBtn = carousel.querySelector('.carousel-prev');
            const nextBtn = carousel.querySelector('.carousel-next');

            if (prevBtn) {
                prevBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    goTo(current - 1);
                });
            }
            if (nextBtn) {
                nextBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    goTo(current + 1);
                });
            }

            // Clic sur un indicateur
            dots.forEach(function(dot, i) {
                dot.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    goTo(i);
                });
            });
        });
    }

    // ────────────────────────────────────────────────────────────────────────
    // Hauteur uniforme quand au moins une actualité a du média
    // ────────────────────────────────────────────────────────────────────────

    /**
     * Si au moins une actualité possède un média (image ou vidéo),
     * toutes les actualités adoptent la même hauteur fixe pour éviter
     * que le bloc ne change de taille lors des transitions.
     */
    function applyUniformHeight() {
        const list = document.querySelector('#actualitesList');
        if (!list) return;

        if (list.querySelectorAll('.actualitesListItem[data-hasmedia="true"]').length > 0) {
            list.classList.add('has-media-items');
        }

        // Neutralise bottom:0 (CSS) pour que chaque item se dimensionne à son contenu
        const items = list.querySelectorAll('.actualitesListItem');
        items.forEach(function(item) {
            item.style.setProperty('bottom', 'auto', 'important');
        });

        // Lecture synchrone (offsetHeight provoque un reflow) — fonctionne sur visibility:hidden
        let maxH = 0;
        items.forEach(function(item) {
            const h = item.offsetHeight;
            if (h > maxH) maxH = h;
        });

        // Restaure bottom:0 via CSS
        items.forEach(function(item) {
            item.style.removeProperty('bottom');
        });

        if (maxH > 0) {
            list.style.minHeight = maxH + 'px';
        }
    }

    // ────────────────────────────────────────────────────────────────────────
    // Helpers partagés pour les points de navigation
    // ────────────────────────────────────────────────────────────────────────

    function setDotActive(dotEl, color) {
        dotEl.classList.remove('grey', 'sml');
        dotEl.classList.add('green', 'lrg');
        dotEl.style.backgroundColor = color;
    }

    function setDotInactive(dotEl) {
        dotEl.classList.remove('green', 'lrg');
        dotEl.classList.add('grey', 'sml');
        dotEl.style.backgroundColor = '#a9a9a9';
    }

    function showItem(n) {
        const el = document.querySelector(`#actualitesList li.actualitesListItem:nth-child(${n})`);
        if (!el) return;
        if (el.classList.contains('glideDisappear')) {
            el.classList.remove('glideDisappear');
        }
        el.classList.add('glideAppear');
    }

    function hideItem(n) {
        const el = document.querySelector(`#actualitesList li.actualitesListItem:nth-child(${n})`);
        if (!el) return;
        if (el.classList.contains('glideAppear')) {
            el.classList.remove('glideAppear');
        }
        el.classList.add('glideDisappear');
    }

    // ────────────────────────────────────────────────────────────────────────
    // Point d'entrée principal
    // ────────────────────────────────────────────────────────────────────────

    const init = (clr, cycle) => {

        globalNewsSettngsDatas.push(clr);
        globalNewsSettngsDatas.push(cycle);

        // Nombre total d'actualités visibles
        const nbNews  = document.querySelectorAll('#actualitesList li').length;
        var activeNews = 1;

        // ── Génération des points de navigation ──────────────────────────
        let dots = '';
        for (var i = 1; i <= nbNews; i++) {
            dots += i === 1
                ? `<span class="dots green lrg" data-news="${i}"></span>`
                : `<span class="dots grey sml"  data-news="${i}"></span>`;
        }
        document.getElementById('dotContainer').innerHTML = dots;

        // Couleur du premier point actif
        const firstGreenDot = document.querySelector('#dotContainer .dots.green');
        if (firstGreenDot) {
            firstGreenDot.style.backgroundColor = globalNewsSettngsDatas[0];
        }

        // ── Couleur de la catégorie et du label ──────────────────────────
        const iconEl = document.querySelector('#newsLabelContent .actualitesIcon');
        if (iconEl) {
            iconEl.style.backgroundColor = globalNewsSettngsDatas[0];
        }

        document.querySelectorAll('#actualitesList .actualitesListItem .actualitesCategories').forEach(function(cat) {
            cat.style.color = globalNewsSettngsDatas[0];
        });

        // ── Hover sur les items avec lien ────────────────────────────────
        document.querySelectorAll('#actualitesList .actualitesListItem').forEach(function(itm) {
            const link    = itm.querySelector('a');
            const hasLink = link && link.getAttribute('href') !== '#';
            if (hasLink) {
                itm.addEventListener('mouseenter', function() {
                    this.style.textDecoration = 'underline';
                    this.style.color          = globalNewsSettngsDatas[0];
                });
                itm.addEventListener('mouseleave', function() {
                    this.style.textDecoration = 'none';
                    this.style.color          = 'initial';
                });
            }
        });

        // ── Clic sur les points de navigation ────────────────────────────
        document.querySelectorAll('#dotContainer span.dots').forEach(function(dot) {
            dot.addEventListener('click', function(e) {
                e.preventDefault();

                // Masquer l'actualité courante
                hideItem(activeNews);
                setDotInactive(document.querySelector(`#dotContainer span.dots:nth-child(${activeNews})`));

                // Passer à l'actualité cliquée
                activeNews = parseInt(this.getAttribute('data-news'));

                showItem(activeNews);
                setDotActive(
                    document.querySelector(`#dotContainer span.dots:nth-child(${activeNews})`),
                    globalNewsSettngsDatas[0]
                );

                dotClicked = true;
            }, false);
        });

        // ── Pause au survol ───────────────────────────────────────────────
        // On surveille #newsList pour couvrir texte, images et vidéos.
        var newsListEl = document.querySelector('#newsList');
        if (newsListEl) {
            newsListEl.addEventListener('mouseenter', function() {
                paused = true;
            });
            newsListEl.addEventListener('mouseleave', function() {
                paused = false;
            });
        }

        // ── Application hauteur uniforme, carrousels et mode responsive ──
        // initResponsive() pose .is-narrow de façon synchrone avant la mesure
        initCarousels();
        initResponsive();
        applyUniformHeight();

        // ── Boucle de défilement automatique ─────────────────────────────
        function loop() {
            setTimeout(function() {

                // Ne pas avancer si l'utilisateur survole le bloc
                if (!paused) {

                    if (activeNews < nbNews) {

                        showItem(activeNews);
                        setDotActive(
                            document.querySelector(`#dotContainer span.dots:nth-child(${activeNews})`),
                            globalNewsSettngsDatas[0]
                        );

                        setTimeout(function() {
                            // Vérifier encore ici : si pause survenue entre les deux timeouts,
                            // on ne masque pas l'item et on ne passe pas au suivant.
                            if (!paused) {
                                hideItem(activeNews);
                                setDotInactive(document.querySelector(`#dotContainer span.dots:nth-child(${activeNews})`));

                                if (dotClicked) {
                                    activeNews = activeNews >= nbNews ? 1 : activeNews + 1;
                                    dotClicked = false;
                                } else {
                                    activeNews++;
                                }
                            }
                        }, parseInt(globalNewsSettngsDatas[1]));

                    } else if (activeNews === nbNews) {

                        showItem(activeNews);
                        setDotActive(
                            document.querySelector(`#dotContainer span.dots:nth-child(${activeNews})`),
                            globalNewsSettngsDatas[0]
                        );

                        setTimeout(function() {
                            if (!paused) {
                                hideItem(activeNews);
                                setDotInactive(document.querySelector(`#dotContainer span.dots:nth-child(${activeNews})`));

                                if (dotClicked) {
                                    activeNews = activeNews >= nbNews ? 1 : activeNews + 1;
                                    dotClicked = false;
                                } else {
                                    activeNews = 1;
                                }
                            }
                        }, parseInt(globalNewsSettngsDatas[1]));
                    }
                }

                loop();

            }, parseInt(globalNewsSettngsDatas[1]));
        }

        loop();
    };

    return {
        init: function (clr, cycle) {
            init(clr, cycle);
        }
    };
});

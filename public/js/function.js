/**
 * Public site behaviour: only the template features the pages actually use.
 *
 * Bundled with the libraries into public/js/site.min.js (see resources/site-assets.json).
 * After editing this file run `npm run build:site`; SiteAssetsTest fails until you do.
 */
(function ($) {
    'use strict';

    /**
     * Fades the loading screen out as soon as the page is ready. It used to wait for every image,
     * which kept a yellow screen up for seconds on a phone (custom CSS hides it after 3 s anyway).
     */
    function initPreloader() {
        $('.preloader').fadeOut(600);
    }

    /** Mobile menu: SlickNav copies #menu into .responsive-menu. Its button only shows an icon, so it gets a name. */
    function initMobileMenu() {
        $('#menu').slicknav({ label: '', prependTo: '.responsive-menu' });
        $('.slicknav_btn').attr('aria-label', 'Menu');
    }

    /**
     * Home hero video on phones. The full video's <source> is for 768px and up, so a phone first
     * shows the poster; once the page has loaded it plays the small phone cut (data-mobile-src).
     * Visitors who asked to save data or to reduce motion keep the poster.
     */
    function initHeroVideo() {
        var video = document.getElementById('myVideo');
        var src = video && video.getAttribute('data-mobile-src');
        var saveData = navigator.connection && navigator.connection.saveData;

        if (!src || saveData
            || !window.matchMedia('(max-width: 767px)').matches
            || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return;
        }

        function start() {
            video.src = src;
            var playing = video.play();
            if (playing) {
                playing.catch(function () {}); // e.g. iPhone Low Power Mode blocks autoplay; the poster stays
            }
        }

        if (document.readyState === 'complete') {
            start();
        } else {
            window.addEventListener('load', start);
        }
    }

    /** Client logo carousel (partials/client-logos). */
    function initClientLogoSlider() {
        var slider = document.querySelector('.trusted-clients-slider .swiper');
        if (!slider) {
            return;
        }

        new Swiper(slider, {
            slidesPerView: 2,
            speed: 2000,
            spaceBetween: 30,
            loop: true,
            autoplay: { delay: 5000 },
            breakpoints: {
                768: { slidesPerView: 4 },
                991: { slidesPerView: 5 },
            },
        });
    }

    /** Skill bars (team member page) fill up when scrolled into view. */
    function initSkillBars() {
        var $section = $('.skills-progress-bar');
        if (!$section.length) {
            return;
        }

        $section.waypoint(function () {
            $('.skillbar').each(function () {
                $(this).find('.count-bar').animate({ width: $(this).attr('data-percent') }, 2000);
            });
        }, { offset: '50%' });
    }

    /** CTA numbers count up when scrolled into view (partials/cta). */
    function initCounters() {
        var $counters = $('.counter');
        if ($counters.length) {
            $counters.counterUp({ delay: 6, time: 3000 });
        }
    }

    /** Images inside .reveal slide in when scrolled into view. */
    function initImageReveal() {
        document.querySelectorAll('.reveal').forEach(function (container) {
            gsap.timeline({ scrollTrigger: { trigger: container, toggleActions: 'play none none none' } })
                .set(container, { autoAlpha: 1 })
                .from(container, { duration: 1, xPercent: -100, ease: 'power2.out' })
                .from(container.querySelector('img'), { duration: 1, xPercent: 100, ease: 'power2.out' }, '<');
        });
    }

    /** Headings with .text-anime-style-2 appear letter by letter when scrolled into view. */
    function initHeadingAnimation() {
        document.querySelectorAll('.text-anime-style-2').forEach(function (heading) {
            var split = new SplitText(heading, { type: 'chars, words' });

            gsap.from(split.chars, {
                duration: 1,
                delay: 0.1,
                x: 20,
                autoAlpha: 0,
                stagger: 0.03,
                ease: 'power2.out',
                scrollTrigger: { trigger: heading, start: 'top 85%' },
            });
        });
    }

    /** Creative tools: the hovered tool becomes the highlighted one. */
    function initCreativeTools() {
        var $tools = $('.creative-tools-box .creative-tool-item');

        $tools.on('mouseenter', function () {
            $tools.removeClass('active');
            $(this).addClass('active');
        });
    }

    /**
     * YouTube projects show a thumbnail (partials/work-item). The player, which weighs
     * far more than the page around it, is only loaded when the visitor clicks.
     */
    function initVideoThumbnails() {
        $(document).on('click', '.video-facade', function () {
            var $thumbnail = $(this);

            $('<iframe allowfullscreen>')
                .attr({
                    src: $thumbnail.attr('data-embed'),
                    title: $thumbnail.attr('data-title'),
                    allow: 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share',
                    referrerpolicy: 'strict-origin-when-cross-origin',
                })
                .replaceAll($thumbnail);
        });
    }

    /**
     * Project modal (#portfolioModal in layouts/app), opened from a project card.
     * Built with text()/attr() so titles and descriptions can never inject HTML;
     * the media source is prepared by the server (YouTube embed or image URL).
     */
    function initProjectModal() {
        var $modal = $('#portfolioModal');
        if (!$modal.length) {
            return;
        }

        function open($trigger) {
            var title = $trigger.attr('data-title') || '';
            var description = $trigger.attr('data-desc') || '';
            var link = $trigger.attr('data-link') || '';
            var mediaSrc = $trigger.attr('data-media-src') || '';
            var isVideo = $trigger.attr('data-media-type') === 'video';

            var $details = $('#modalProjectDetails').empty();
            [['Project Name:', title], ['Category:', $trigger.attr('data-category') || '']].forEach(function (row) {
                $('<div class="category-list-item">').append($('<h3>').text(row[0]), $('<p>').text(row[1])).appendTo($details);
            });

            var $overview = $('#modalProjectOverview').empty().append('<h2>Project <span>Overview</span></h2>');
            if (mediaSrc) {
                var $media = isVideo
                    ? $('<iframe allowfullscreen>').attr({ src: mediaSrc, title: title })
                    : $('<img>').attr({ src: mediaSrc, alt: title });
                $('<div class="project-media">').append($media).appendTo($overview);
            }
            if (description) {
                $('<p>').text(description).appendTo($overview);
            }
            if (/^https?:\/\//i.test(link)) {
                $('<a class="visit-btn" target="_blank" rel="noopener">').attr('href', link).text('Visit Project').appendTo($overview);
            }

            $modal.addClass('active');
            $('body').css({ overflow: 'hidden', 'overflow-x': 'clip' });
        }

        function close() {
            $modal.addClass('closing');
            setTimeout(function () {
                $modal.removeClass('active closing');
                $('#modalProjectOverview').empty(); // stops a playing video
                $('body').css({ 'overflow-y': 'auto', 'overflow-x': 'clip' });
            }, 300);
        }

        $(document)
            .on('click', '.open-portfolio-modal', function (event) {
                event.preventDefault();
                open($(this));
            })
            .on('click', '.portfolio-modal-close, .portfolio-modal-overlay', close)
            .on('keydown', function (event) {
                if (event.key === 'Escape' && $modal.hasClass('active')) {
                    close();
                }
            });
    }

    $(function () {
        initPreloader();
        gsap.registerPlugin(ScrollTrigger);

        initMobileMenu();
        initHeroVideo();
        initClientLogoSlider();
        initSkillBars();
        initCounters();
        initImageReveal();
        initHeadingAnimation();
        initCreativeTools();
        initVideoThumbnails();
        initProjectModal();
    });
})(jQuery);

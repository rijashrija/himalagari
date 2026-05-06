/**
 * load-more.js — Global "Load More / Show Less" handler for .packages-wrapper sections
 */
(function () {
    function initLoadMore() {
        document.querySelectorAll(".packages-wrapper").forEach(function (section) {
            var btn = section.querySelector(".js-load-more-packages");
            var cards = Array.from(section.querySelectorAll(".package-card, .deal-card"));

            if (!btn || !cards.length) return;

            var initial = parseInt(btn.getAttribute("data-initial"), 10) || 6;
            var loadMoreWrap = btn.closest(".load-more-wrap");

            // Initial state: hide items > initial
            cards.forEach(function (card, i) {
                if (i >= initial) {
                    card.style.setProperty("display", "none", "important");
                }
            });

            // Hide toggle button if there are not enough items to toggle
            if (cards.length <= initial) {
                if (loadMoreWrap) loadMoreWrap.style.setProperty("display", "none", "important");
                return;
            }

            btn.addEventListener("click", function (e) {
                e.preventDefault();
                var pTag = btn.querySelector("p");
                var isShowingAll = btn.classList.contains("showing-all");

                if (isShowingAll) {
                    // SHOW LESS
                    cards.forEach(function (card, i) {
                        if (i >= initial) {
                            card.style.setProperty("display", "none", "important");
                        }
                    });
                    btn.classList.remove("showing-all");
                    if (pTag) pTag.innerText = "Load More Packages";

                    // Scroll back to title
                    setTimeout(function () {
                        var titleElement = section.querySelector(".page-header") || section.querySelector("header") || section.querySelector("h2") || section;
                        var headerOffset = 110;
                        var elementPosition = titleElement.getBoundingClientRect().top;
                        var offsetPosition = elementPosition + (window.pageYOffset || window.scrollY) - headerOffset;

                        window.scrollTo({
                            top: offsetPosition,
                            behavior: "smooth"
                        });
                    }, 100);
                } else {
                    // SHOW ALL
                    cards.forEach(function (card) {
                        card.style.setProperty("display", "flex", "important");
                    });
                    btn.classList.add("showing-all");
                    if (pTag) pTag.innerText = "Show Less Packages";
                }
            });
        });
    }

    if (document.readyState === "complete" || document.readyState === "interactive") {
        initLoadMore();
    } else {
        document.addEventListener("DOMContentLoaded", initLoadMore);
    }
})();

(function ($) {
    $(document).ready(function () {
        $(".xzoom, .xzoom-gallery").xzoom({
            zoomWidth: 400,
            title: true,
            tint: "#333",
            Xoffset: 15,
        });
        $(".xzoom2, .xzoom-gallery2").xzoom({
            position: "#xzoom2-id",
            tint: "#ffa200",
        });
        $(".xzoom3, .xzoom-gallery3").xzoom({
            position: "lens",
            lensShape: "circle",
            sourceClass: "xzoom-hidden",
        });
        $(".xzoom4, .xzoom-gallery4").xzoom({ tint: "#006699", Xoffset: 15 });
        $(".xzoom5, .xzoom-gallery5").xzoom({ tint: "#006699", Xoffset: 15 });

        // Integration with fancybox plugin
        $("#xzoom-fancy").bind("click", function (event) {
            var xzoom = $(this).data("xzoom");
            xzoom.closezoom();
            $.fancybox.open(xzoom.gallery().cgallery, {
                padding: 0,
                helpers: { overlay: { locked: false } },
            });
            event.preventDefault();
        });

        // Integration with magnific popup plugin
        $("#xzoom-magnific").bind("click", function (event) {
            var xzoom = $(this).data("xzoom");
            xzoom.closezoom();
            var gallery = xzoom.gallery().cgallery;
            var i,
                images = new Array();
            for (i in gallery) {
                images[i] = { src: gallery[i] };
            }
            $.magnificPopup.open({
                items: images,
                type: "image",
                gallery: { enabled: true },
            });
            event.preventDefault();
        });
    });
})(jQuery);
